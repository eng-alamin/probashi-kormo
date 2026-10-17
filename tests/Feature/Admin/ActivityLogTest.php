<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Enums\RoleName;
use App\Livewire\Admin\Applications;
use App\Livewire\Admin\Inquiries;
use App\Livewire\Admin\Jobs;
use App\Livewire\Admin\Logs;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\Trainings;
use App\Livewire\Admin\Users;
use App\Models\ActivityLog;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\Service;
use App\Models\TrainingCourse;
use App\Models\User;
use App\Support\ActivityLogger;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(PermissionSeeder::class);
    }

    /**
     * Note: nothing is logged while nobody is signed in, so records and users made
     * BEFORE calling actingAs() never add log lines. Tests below rely on that.
     */
    private function userWithRole(RoleName $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role->value);

        return $user;
    }

    private function country(): Country
    {
        return Country::create(['name' => 'Saudi Arabia', 'slug' => 'saudi-arabia', 'is_active' => true, 'sort_order' => 1]);
    }

    /**
     * @return list<string>
     */
    private function descriptionsInOrder(): array
    {
        return ActivityLog::latestFirst()->pluck('description')->reverse()->values()->all();
    }

    // ---------- model changes ----------

    public function test_create_update_toggle_and_delete_by_a_signed_in_person_are_logged(): void
    {
        $admin = $this->userWithRole(RoleName::Admin);
        $this->actingAs($admin);

        $country = $this->country();
        $job = JobPost::create(['country_id' => $country->id, 'title' => 'Electrician', 'slug' => 'electrician', 'is_active' => true]);
        $job->update(['title' => 'Senior Electrician', 'vacancies' => 5]);
        $job->update(['is_active' => false]);
        $job->delete();

        $this->assertSame([
            'Created country "Saudi Arabia"',
            'Created job "Electrician"',
            'Updated job "Senior Electrician" (changed: title, vacancies)',
            'Updated job "Senior Electrician" (changed: active)',
            'Deleted job "Senior Electrician"',
        ], $this->descriptionsInOrder());

        $first = ActivityLog::latestFirst()->first();
        $this->assertSame($admin->id, $first->user_id);
        $this->assertSame($admin->name, $first->actor_name);
        $this->assertSame('deleted', $first->event);
        $this->assertSame('job', $first->subject_type);
        $this->assertSame($job->id, $first->subject_id);
    }

    public function test_nothing_is_logged_when_nobody_is_signed_in(): void
    {
        $country = $this->country();
        JobPost::create(['country_id' => $country->id, 'title' => 'Electrician', 'slug' => 'electrician', 'is_active' => true]);
        JobApplication::create(['full_name' => 'Rahim Uddin', 'phone' => '01712345678', 'passport_status' => 'none', 'status' => 'new']);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_status_change_is_described_and_note_text_is_never_stored(): void
    {
        $application = JobApplication::create(['full_name' => 'Rahim Uddin', 'phone' => '01712345678', 'passport_status' => 'none', 'status' => 'new']);
        $this->actingAs($this->userWithRole(RoleName::Staff));

        Livewire::test(Applications\Show::class, ['application' => $application])
            ->set('status', 'contacted')
            ->set('admin_note', 'Called, secret-note-text.')
            ->call('save')
            ->assertHasNoErrors();

        $log = ActivityLog::latestFirst()->first();

        $this->assertSame('status_changed', $log->event);
        $this->assertSame(
            'Changed status of application "Rahim Uddin" from New to Contacted; also changed: admin note',
            $log->description,
        );
        $this->assertSame(0, ActivityLog::where('description', 'like', '%secret-note-text%')->count());
        $this->assertSame(0, ActivityLog::where('description', 'like', '%01712345678%')->count());
    }

    public function test_opening_a_new_inquiry_is_not_logged_but_a_real_status_change_is(): void
    {
        $inquiry = Inquiry::create(['full_name' => 'Karim Mia', 'phone' => '01812345678', 'message' => 'Hello', 'status' => 'new']);
        $this->actingAs($this->userWithRole(RoleName::Staff));

        $component = Livewire::test(Inquiries\Show::class, ['inquiry' => $inquiry]);

        $this->assertSame('read', $inquiry->fresh()->status->value);
        $this->assertSame(0, ActivityLog::count());

        $component->set('status', 'replied')->call('save')->assertHasNoErrors();

        $this->assertSame(
            ['Changed status of inquiry "Karim Mia" from Read to Replied'],
            $this->descriptionsInOrder(),
        );
    }

    public function test_deleting_from_the_list_pages_is_logged(): void
    {
        $country = $this->country();
        $application = JobApplication::create(['full_name' => 'Rahim Uddin', 'phone' => '01712345678', 'passport_status' => 'none', 'status' => 'new']);
        $inquiry = Inquiry::create(['full_name' => 'Karim Mia', 'phone' => '01812345678', 'message' => 'Hello', 'status' => 'new']);
        $job = JobPost::create(['country_id' => $country->id, 'title' => 'Electrician', 'slug' => 'electrician', 'is_active' => true]);
        $service = Service::create(['title' => 'Visa Support', 'slug' => 'visa-support']);
        $course = TrainingCourse::create(['title' => 'Welding', 'slug' => 'welding']);

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        Livewire::test(Applications\Index::class)->call('confirmDelete', $application->id)->call('delete');
        Livewire::test(Inquiries\Index::class)->call('confirmDelete', $inquiry->id)->call('delete');
        Livewire::test(Jobs\Index::class)->call('confirmDelete', $job->id)->call('delete');
        Livewire::test(Services\Index::class)->call('confirmDelete', $service->id)->call('delete');
        Livewire::test(Trainings\Index::class)->call('confirmDelete', $course->id)->call('delete');

        $this->assertModelMissing($application);
        $this->assertModelMissing($course);
        $this->assertSame([
            'Deleted application "Rahim Uddin"',
            'Deleted inquiry "Karim Mia"',
            'Deleted job "Electrician"',
            'Deleted service "Visa Support"',
            'Deleted training course "Welding"',
        ], $this->descriptionsInOrder());
    }

    public function test_password_changes_log_only_the_word_password(): void
    {
        $target = $this->userWithRole(RoleName::Staff);
        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        $target->update(['password' => 'BrandNewSecret123']);

        $log = ActivityLog::latestFirst()->first();

        $this->assertSame('Updated staff user "'.$target->name.'" (changed: password)', $log->description);
        $this->assertSame(0, ActivityLog::where('description', 'like', '%BrandNewSecret123%')->count());
    }

    public function test_logging_never_breaks_the_real_action(): void
    {
        ActivityLog::creating(static function (): void {
            throw new \RuntimeException('the log table is broken');
        });

        $this->actingAs($this->userWithRole(RoleName::Admin));

        $country = $this->country();

        $this->assertModelExists($country);
        $this->assertSame(0, ActivityLog::count());
    }

    // ---------- roles, permissions, sign in ----------

    public function test_creating_a_staff_user_and_changing_their_role_are_logged(): void
    {
        $existing = $this->userWithRole(RoleName::Staff);
        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        Livewire::test(Users\Form::class)
            ->set('name', 'Sabbir Hossain')
            ->set('email', 'sabbir@example.com')
            ->set('password', 'Strong1234pw')
            ->set('password_confirmation', 'Strong1234pw')
            ->set('role', 'Staff')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            ['Created staff user "Sabbir Hossain"', 'Gave role Staff to staff user "Sabbir Hossain"'],
            $this->descriptionsInOrder(),
        );
        $this->assertSame(0, ActivityLog::where('description', 'like', '%Strong1234pw%')->count());

        Livewire::test(Users\Form::class, ['user' => $existing])
            ->set('name', 'Renamed Person')
            ->set('role', 'Admin')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(
            'Changed role of staff user "Renamed Person" from Staff to Admin',
            ActivityLog::where('event', 'role_changed')->latestFirst()->first()->description,
        );

        // Saving again without touching the role writes no new role line.
        Livewire::test(Users\Form::class, ['user' => $existing->fresh()])
            ->set('name', 'Renamed Again')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, ActivityLog::where('event', 'role_changed')->count());
    }

    public function test_permission_changes_list_what_was_added_and_removed(): void
    {
        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));
        $role = Role::findByName(RoleName::Staff->value);

        Livewire::test(Roles\Form::class, ['role' => $role])
            ->set('selected', ['applications.view', 'gallery.view'])
            ->call('save')
            ->assertHasNoErrors();

        $log = ActivityLog::where('event', 'permissions_changed')->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('Changed permissions of Staff role', $log->description);
        $this->assertStringContainsString('added gallery.view', $log->description);
        $this->assertStringContainsString('removed', $log->description);
        $this->assertStringContainsString('inquiries.update', $log->description);

        // The same selection again changes nothing, so nothing is logged.
        Livewire::test(Roles\Form::class, ['role' => $role->fresh()])
            ->set('selected', ['applications.view', 'gallery.view'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, ActivityLog::where('event', 'permissions_changed')->count());
    }

    public function test_sign_in_and_sign_out_of_staff_are_logged_but_other_accounts_are_not(): void
    {
        $staff = $this->userWithRole(RoleName::Staff);
        $stranger = User::factory()->create(['is_active' => true]);

        event(new Login('web', $staff, false));
        event(new Logout('web', $staff));
        event(new Login('web', $stranger, false));
        event(new Logout('web', $stranger));

        $this->assertSame(
            ['signed_in', 'signed_out'],
            ActivityLog::latestFirst()->pluck('event')->reverse()->values()->all(),
        );
        $this->assertSame($staff->id, ActivityLog::first()->user_id);
    }

    // ---------- the Activity Log page ----------

    public function test_logs_permission_exists_but_only_super_admin_has_it_by_default(): void
    {
        $this->assertTrue(Permission::where('name', 'logs.view')->exists());
        $this->assertFalse(Role::findByName(RoleName::Admin->value)->hasPermissionTo('logs.view'));
        $this->assertFalse(Role::findByName(RoleName::Staff->value)->hasPermissionTo('logs.view'));
    }

    public function test_only_super_admin_can_open_the_page_by_default(): void
    {
        foreach ([RoleName::Admin, RoleName::Staff] as $role) {
            $this->actingAs($this->userWithRole($role));

            $this->get(route('admin.logs.index'))->assertForbidden();
        }

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        $this->get(route('admin.logs.index'))->assertOk()->assertSee('Activity Log');
    }

    public function test_an_admin_who_was_given_the_permission_can_open_the_page_and_sees_the_menu_link(): void
    {
        $admin = $this->userWithRole(RoleName::Admin);

        $this->actingAs($admin);
        $this->get(route('admin.dashboard'))->assertOk()->assertDontSee(route('admin.logs.index'));

        Role::findByName(RoleName::Admin->value)->givePermissionTo('logs.view');

        $this->actingAs($admin->fresh());
        $this->get(route('admin.logs.index'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk()->assertSee(route('admin.logs.index'));
    }

    public function test_the_page_filters_by_event_search_and_staff(): void
    {
        $other = $this->userWithRole(RoleName::Staff);
        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        ActivityLogger::record(ActivityEvent::Created, 'Created job "Alpha"', 'job', 1);
        ActivityLogger::record(ActivityEvent::Deleted, 'Deleted job "Beta"', 'job', 2);
        ActivityLogger::record(ActivityEvent::Updated, 'Updated country "Gamma" (changed: name)', 'country', 3, $other);

        Livewire::test(Logs\Index::class)
            ->assertSee('Alpha')->assertSee('Beta')->assertSee('Gamma')
            ->set('kind', 'deleted')
            ->assertSee('Beta')->assertDontSee('Alpha')->assertDontSee('Gamma')
            ->set('kind', '')
            ->set('search', 'Alpha')
            ->assertSee('Alpha')->assertDontSee('Beta')
            ->set('search', '')
            ->set('staff', (string) $other->id)
            ->assertSee('Gamma')->assertDontSee('Alpha')->assertDontSee('Beta');
    }

    // ---------- pruning ----------

    public function test_prune_removes_only_lines_older_than_the_limit(): void
    {
        $line = static fn (int $daysAgo): array => [
            'event' => 'updated',
            'description' => "Line from {$daysAgo} days ago",
            'created_at' => now()->subDays($daysAgo),
        ];

        ActivityLog::create($line(200));
        ActivityLog::create($line(40));
        ActivityLog::create($line(1));

        $this->artisan('activity:prune')->assertSuccessful();

        $this->assertSame(
            ['Line from 1 days ago', 'Line from 40 days ago'],
            ActivityLog::latestFirst()->pluck('description')->all(),
        );

        $this->artisan('activity:prune', ['--days' => '30'])->assertSuccessful();

        $this->assertSame(['Line from 1 days ago'], ActivityLog::pluck('description')->all());
    }

    public function test_prune_rejects_a_bad_number_of_days(): void
    {
        ActivityLog::create(['event' => 'updated', 'description' => 'Old line', 'created_at' => now()->subDays(500)]);

        $this->artisan('activity:prune', ['--days' => '0'])->assertFailed();
        $this->artisan('activity:prune', ['--days' => 'abc'])->assertFailed();

        $this->assertSame(1, ActivityLog::count());
    }
}
