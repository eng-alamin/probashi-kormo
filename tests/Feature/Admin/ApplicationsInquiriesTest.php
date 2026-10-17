<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Applications;
use App\Livewire\Admin\Inquiries;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApplicationsInquiriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(PermissionSeeder::class);
    }

    private function userWithRole(RoleName $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role->value);

        return $user;
    }

    private function application(string $name, string $status = 'new'): JobApplication
    {
        return JobApplication::create([
            'full_name' => $name,
            'phone' => '01712345678',
            'passport_status' => 'none',
            'status' => $status,
        ]);
    }

    private function inquiry(string $name): Inquiry
    {
        return Inquiry::create([
            'full_name' => $name,
            'phone' => '01812345678',
            'message' => 'I want to know about jobs in Malaysia.',
            'status' => 'new',
        ]);
    }

    public function test_default_roles_get_the_expected_permissions(): void
    {
        $staff = Role::findByName(RoleName::Staff->value, 'web');
        $admin = Role::findByName(RoleName::Admin->value, 'web');

        $this->assertTrue($staff->hasPermissionTo('applications.update'));
        $this->assertFalse($staff->hasPermissionTo('applications.delete'));
        $this->assertTrue($admin->hasPermissionTo('applications.delete'));
        $this->assertFalse($admin->hasPermissionTo('users.view'));
    }

    public function test_staff_can_open_the_applications_page_and_see_applicants(): void
    {
        $this->application('Rahim Uddin');

        $this->actingAs($this->userWithRole(RoleName::Staff))
            ->get(route('admin.applications.index'))
            ->assertOk()
            ->assertSee('Rahim Uddin');
    }

    public function test_user_without_the_permission_gets_403(): void
    {
        Role::findByName(RoleName::Staff->value, 'web')->revokePermissionTo('applications.view');

        $this->actingAs($this->userWithRole(RoleName::Staff))
            ->get(route('admin.applications.index'))
            ->assertForbidden();
    }

    public function test_applications_can_be_searched_and_filtered(): void
    {
        $this->application('Rahim Uddin');
        $this->application('Karim Mia', 'placed');

        $this->actingAs($this->userWithRole(RoleName::Staff));

        Livewire::test(Applications\Index::class)
            ->set('search', 'Rahim')
            ->assertSee('Rahim Uddin')
            ->assertDontSee('Karim Mia')
            ->set('search', '')
            ->set('status', 'placed')
            ->assertSee('Karim Mia')
            ->assertDontSee('Rahim Uddin');
    }

    public function test_staff_can_update_status_and_note(): void
    {
        $application = $this->application('Rahim Uddin');
        $staff = $this->userWithRole(RoleName::Staff);

        $this->actingAs($staff);

        Livewire::test(Applications\Show::class, ['application' => $application])
            ->set('status', 'contacted')
            ->set('admin_note', 'Called, will visit the office.')
            ->call('save')
            ->assertHasNoErrors();

        $application->refresh();
        $this->assertSame('contacted', $application->status->value);
        $this->assertSame('Called, will visit the office.', $application->admin_note);
        $this->assertSame($staff->id, $application->handled_by);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $application = $this->application('Rahim Uddin');

        $this->actingAs($this->userWithRole(RoleName::Staff));

        Livewire::test(Applications\Show::class, ['application' => $application])
            ->set('status', 'hacked')
            ->call('save')
            ->assertHasErrors(['status']);
    }

    public function test_staff_cannot_delete_but_admin_can(): void
    {
        $application = $this->application('Rahim Uddin');

        $this->actingAs($this->userWithRole(RoleName::Staff));
        Livewire::test(Applications\Index::class)
            ->call('confirmDelete', $application->id)
            ->assertForbidden();
        $this->assertDatabaseHas('job_applications', ['id' => $application->id]);

        $this->actingAs($this->userWithRole(RoleName::Admin));
        Livewire::test(Applications\Index::class)
            ->call('confirmDelete', $application->id)
            ->call('delete');
        $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
    }

    public function test_opening_a_new_inquiry_marks_it_read(): void
    {
        $inquiry = $this->inquiry('Jamal Hossain');

        $this->actingAs($this->userWithRole(RoleName::Staff))
            ->get(route('admin.inquiries.show', $inquiry))
            ->assertOk()
            ->assertSee('Jamal Hossain');

        $inquiry->refresh();
        $this->assertSame('read', $inquiry->status->value);
        $this->assertNotNull($inquiry->read_at);
    }

    public function test_staff_can_mark_an_inquiry_replied(): void
    {
        $inquiry = $this->inquiry('Jamal Hossain');

        $this->actingAs($this->userWithRole(RoleName::Staff));

        Livewire::test(Inquiries\Show::class, ['inquiry' => $inquiry])
            ->set('status', 'replied')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('replied', $inquiry->fresh()->status->value);
    }

    public function test_inquiries_can_be_searched(): void
    {
        $this->inquiry('Jamal Hossain');
        $this->inquiry('Salma Akter');

        $this->actingAs($this->userWithRole(RoleName::Staff));

        Livewire::test(Inquiries\Index::class)
            ->set('search', 'Salma')
            ->assertSee('Salma Akter')
            ->assertDontSee('Jamal Hossain');
    }
}
