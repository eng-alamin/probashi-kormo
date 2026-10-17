<?php

namespace Tests\Feature\Admin;

use App\Enums\ActivityEvent;
use App\Enums\RoleName;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\TrainingCourse;
use App\Models\User;
use App\Support\ActivityLogger;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    private function inquiry(string $name, string $status = 'new'): Inquiry
    {
        return Inquiry::create([
            'full_name' => $name,
            'phone' => '01812345678',
            'message' => 'I want to know about jobs in Malaysia.',
            'status' => $status,
        ]);
    }

    public function test_dashboard_shows_real_numbers(): void
    {
        $country = Country::create(['name' => 'Saudi Arabia', 'slug' => 'saudi-arabia', 'is_active' => true, 'sort_order' => 1]);
        JobPost::create(['country_id' => $country->id, 'title' => 'Electrician', 'slug' => 'electrician', 'is_active' => true]);
        JobPost::create(['country_id' => $country->id, 'title' => 'Welder', 'slug' => 'welder', 'is_active' => true]);
        JobPost::create(['country_id' => $country->id, 'title' => 'Old job', 'slug' => 'old-job', 'is_active' => false]);
        TrainingCourse::create(['title' => 'Welding', 'slug' => 'welding', 'is_active' => true]);
        TrainingCourse::create(['title' => 'Hidden course', 'slug' => 'hidden-course', 'is_active' => false]);
        $this->application('Rahim Uddin', 'new');
        $this->application('Karim Mia', 'new');
        $this->application('Salma Begum', 'placed');
        $this->inquiry('Jamal Hossain', 'new');
        $this->inquiry('Nasrin Akter', 'read');

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('<div class="label">Applications</div><div class="value">3</div>', false)
            ->assertSee('<div class="label">Open Jobs</div><div class="value">2</div>', false)
            ->assertSee('<div class="label">Training Courses</div><div class="value">1</div>', false)
            ->assertSee('<div class="label">New Messages</div><div class="value">1</div>', false)
            ->assertSee('2 new · 3 this week')
            ->assertSee('of 3 jobs')
            ->assertSee('of 2 courses visible')
            ->assertSee('of 2 messages')
            // 2 of 3 applications are "new", 1 of 3 is "placed".
            ->assertSee('aria-valuenow="67"', false)
            ->assertSee('aria-valuenow="33"', false);
    }

    public function test_dashboard_lists_the_latest_applications_and_messages(): void
    {
        $this->application('Rahim Uddin');
        $this->inquiry('Jamal Hossain');

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent applications')
            ->assertSee('Rahim Uddin')
            ->assertSee('Recent messages')
            ->assertSee('Jamal Hossain');
    }

    public function test_the_last_seven_days_chart_counts_todays_applications(): void
    {
        $this->application('Rahim Uddin');

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        // Today is always the last bar of the chart.
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(now()->format('D').' 1"', false);
    }

    public function test_an_empty_database_still_renders(): void
    {
        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('<div class="label">Applications</div><div class="value">0</div>', false)
            ->assertSee('No applications yet')
            ->assertSee('No messages yet')
            ->assertSee('No activity yet');
    }

    public function test_staff_only_sees_the_sections_they_may_open(): void
    {
        $this->application('Rahim Uddin');
        $this->inquiry('Jamal Hossain');

        $this->actingAs($this->userWithRole(RoleName::Staff));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('<div class="label">Applications</div>', false)
            ->assertSee('<div class="label">New Messages</div>', false)
            ->assertSee('Rahim Uddin')
            ->assertDontSee('Open Jobs')
            ->assertDontSee('Training Courses')
            ->assertDontSee('Recent activity');
    }

    public function test_recent_activity_is_for_people_with_the_log_permission_only(): void
    {
        $super = $this->userWithRole(RoleName::SuperAdmin);
        $admin = $this->userWithRole(RoleName::Admin);

        $this->actingAs($super);
        ActivityLogger::record(ActivityEvent::Deleted, 'Deleted job "Alpha"', 'job', 1);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent activity')
            ->assertSee('Deleted job "Alpha"');

        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Recent activity')
            ->assertDontSee('Alpha');
    }

    public function test_a_role_without_any_dashboard_section_sees_a_welcome_message(): void
    {
        Role::findByName(RoleName::Staff->value)->syncPermissions(['gallery.view']);
        $this->actingAs($this->userWithRole(RoleName::Staff));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Use the menu to open the sections you have access to.')
            ->assertDontSee('Recent applications')
            ->assertDontSee('Recent messages');
    }
}
