<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\Trainings;
use App\Models\Service;
use App\Models\TrainingCourse;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrainingsServicesAdminTest extends TestCase
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

    private function course(string $title = 'Electrician', string $slug = 'electrician'): TrainingCourse
    {
        return TrainingCourse::create(['title' => $title, 'slug' => $slug, 'is_active' => true, 'sort_order' => 1]);
    }

    private function service(string $title = 'Visa Processing', string $slug = 'visa-processing'): Service
    {
        return Service::create(['title' => $title, 'slug' => $slug, 'is_active' => true, 'sort_order' => 1]);
    }

    public function test_staff_cannot_open_training_or_services(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Staff));

        $this->get(route('admin.trainings.index'))->assertForbidden();
        $this->get(route('admin.trainings.create'))->assertForbidden();
        $this->get(route('admin.services.index'))->assertForbidden();
        $this->get(route('admin.services.create'))->assertForbidden();
    }

    public function test_admin_can_open_the_lists(): void
    {
        $this->course();
        $this->service();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->get(route('admin.trainings.index'))->assertOk()->assertSee('Electrician');
        $this->get(route('admin.services.index'))->assertOk()->assertSee('Visa Processing');
    }

    public function test_admin_can_create_a_course_with_details(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Trainings\Form::class)
            ->set('title', 'Plumbing Basics')
            ->set('icon', '🔧')
            ->set('duration_weeks', '8')
            ->set('fee', '12000')
            ->set('learn_points', "Pipes\nFittings")
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.trainings.index'));

        $course = TrainingCourse::firstWhere('title', 'Plumbing Basics');
        $this->assertSame('plumbing-basics', $course->slug);
        $this->assertSame(8, $course->duration_weeks);
        $this->assertSame(12000, $course->fee);
        $this->assertSame("Pipes\nFittings", $course->learn_points);
    }

    public function test_blank_duration_and_fee_are_saved_as_null(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Trainings\Form::class)
            ->set('title', 'Welding')
            ->call('save')
            ->assertHasNoErrors();

        $course = TrainingCourse::firstWhere('title', 'Welding');
        $this->assertNull($course->duration_weeks);
        $this->assertNull($course->fee);
    }

    public function test_course_validation(): void
    {
        $this->course();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Trainings\Form::class)
            ->set('title', '')
            ->set('slug', 'electrician')
            ->set('duration_weeks', '0')
            ->set('fee', '-5')
            ->call('save')
            ->assertHasErrors(['title', 'slug', 'duration_weeks', 'fee']);

        Livewire::test(Trainings\Form::class)
            ->set('title', 'Valid')
            ->set('slug', 'Bad Slug!')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_editing_a_course_keeps_its_slug(): void
    {
        $course = $this->course();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Trainings\Form::class, ['course' => $course])
            ->assertSet('title', 'Electrician')
            ->set('title', 'Electrician Pro')
            ->set('class_days', 'Sat - Thu')
            ->call('save')
            ->assertHasNoErrors();

        $course->refresh();
        $this->assertSame('Electrician Pro', $course->title);
        $this->assertSame('electrician', $course->slug);
        $this->assertSame('Sat - Thu', $course->class_days);
    }

    public function test_hiding_a_course_removes_it_from_the_public_site_and_delete_works(): void
    {
        $course = $this->course();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->get(route('training.show', $course))->assertOk();

        Livewire::test(Trainings\Index::class)->call('toggleActive', $course->id);
        $this->get(route('training.show', $course))->assertNotFound();

        Livewire::test(Trainings\Index::class)
            ->call('confirmDelete', $course->id)
            ->call('delete');
        $this->assertDatabaseMissing('training_courses', ['id' => $course->id]);
    }

    public function test_admin_can_create_and_edit_a_service(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Services\Form::class)
            ->set('title', 'Medical Checkup Support')
            ->set('what_we_do', "Book a clinic\nCollect the report")
            ->set('sort_order', '4')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.services.index'));

        $service = Service::firstWhere('title', 'Medical Checkup Support');
        $this->assertSame('medical-checkup-support', $service->slug);
        $this->assertSame(4, $service->sort_order);

        Livewire::test(Services\Form::class, ['service' => $service])
            ->set('summary', 'Help with medical tests')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Help with medical tests', $service->fresh()->summary);
    }

    public function test_service_slug_must_be_unique(): void
    {
        $this->service();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Services\Form::class)
            ->set('title', 'Another')
            ->set('slug', 'visa-processing')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_hiding_and_deleting_a_service(): void
    {
        $service = $this->service();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->get(route('services.show', $service))->assertOk();

        Livewire::test(Services\Index::class)->call('toggleActive', $service->id);
        $this->get(route('services.show', $service))->assertNotFound();

        Livewire::test(Services\Index::class)
            ->call('confirmDelete', $service->id)
            ->call('delete');
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
