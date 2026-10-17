<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Countries;
use App\Livewire\Admin\Jobs;
use App\Models\Country;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobsCountriesAdminTest extends TestCase
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

    private function country(string $name = 'Saudi Arabia', string $slug = 'saudi-arabia'): Country
    {
        return Country::create(['name' => $name, 'slug' => $slug, 'is_active' => true, 'sort_order' => 1]);
    }

    private function job(Country $country, string $title = 'Electrician', string $slug = 'electrician'): JobPost
    {
        return JobPost::create(['country_id' => $country->id, 'title' => $title, 'slug' => $slug, 'is_active' => true]);
    }

    public function test_staff_cannot_open_jobs_or_countries(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Staff));

        $this->get(route('admin.jobs.index'))->assertForbidden();
        $this->get(route('admin.jobs.create'))->assertForbidden();
        $this->get(route('admin.countries.index'))->assertForbidden();
    }

    public function test_admin_can_open_the_lists(): void
    {
        $country = $this->country();
        $this->job($country);

        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->get(route('admin.jobs.index'))->assertOk()->assertSee('Electrician');
        $this->get(route('admin.countries.index'))->assertOk()->assertSee('Saudi Arabia');
    }

    public function test_admin_can_create_a_job_and_the_slug_is_made_from_the_title(): void
    {
        $country = $this->country();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Form::class)
            ->set('country_id', (string) $country->id)
            ->set('title', 'Senior Electrician')
            ->set('salary_min', '1200')
            ->set('salary_max', '1800')
            ->set('salary_currency', 'sar')
            ->set('deadline', '2030-01-31')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.jobs.index'));

        $job = JobPost::firstWhere('title', 'Senior Electrician');
        $this->assertNotNull($job);
        $this->assertSame('senior-electrician', $job->slug);
        $this->assertSame('SAR', $job->salary_currency);
        $this->assertSame(1200, $job->salary_min);
        $this->assertSame('2030-01-31', $job->deadline->format('Y-m-d'));
        $this->assertTrue($job->is_active);
    }

    public function test_generated_slugs_stay_unique(): void
    {
        $country = $this->country();
        $this->job($country, 'Welder', 'welder');
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Form::class)
            ->set('country_id', (string) $country->id)
            ->set('title', 'Welder')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('job_posts', ['slug' => 'welder-2']);
    }

    public function test_bangla_only_title_still_gets_a_valid_slug(): void
    {
        $country = $this->country();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Form::class)
            ->set('country_id', (string) $country->id)
            ->set('title', 'ইলেকট্রিশিয়ান দরকার')
            ->call('save')
            ->assertHasNoErrors();

        $slug = JobPost::firstOrFail()->slug;
        $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }

    public function test_job_validation(): void
    {
        $country = $this->country();
        $this->job($country, 'Welder', 'welder');
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Form::class)
            ->set('title', 'ab')
            ->set('slug', 'welder')
            ->set('salary_min', '2000')
            ->set('salary_max', '1000')
            ->set('salary_currency', 'SA')
            ->call('save')
            ->assertHasErrors(['country_id', 'title', 'slug', 'salary_max', 'salary_currency']);

        Livewire::test(Jobs\Form::class)
            ->set('country_id', '99999')
            ->set('title', 'Valid title')
            ->call('save')
            ->assertHasErrors(['country_id']);

        $this->assertSame(1, JobPost::count());
    }

    public function test_admin_can_edit_a_job_without_changing_its_slug(): void
    {
        $country = $this->country();
        $job = $this->job($country);
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Form::class, ['job' => $job])
            ->assertSet('title', 'Electrician')
            ->set('title', 'Electrician (Industrial)')
            ->set('vacancies', '25')
            ->call('save')
            ->assertHasNoErrors();

        $job->refresh();
        $this->assertSame('Electrician (Industrial)', $job->title);
        $this->assertSame('electrician', $job->slug);
        $this->assertSame(25, $job->vacancies);
    }

    public function test_toggle_visibility_and_delete_keep_applications(): void
    {
        $country = $this->country();
        $job = $this->job($country);
        $application = JobApplication::create([
            'job_post_id' => $job->id,
            'full_name' => 'Rahim',
            'phone' => '01712345678',
            'passport_status' => 'none',
            'status' => 'new',
        ]);
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Index::class)
            ->call('toggleActive', $job->id)
            ->call('confirmDelete', $job->id)
            ->call('delete');

        $this->assertDatabaseMissing('job_posts', ['id' => $job->id]);
        $this->assertDatabaseHas('job_applications', ['id' => $application->id, 'job_post_id' => null]);
    }

    public function test_job_list_filters(): void
    {
        $country = $this->country();
        $this->job($country, 'Open Welder', 'open-welder');
        JobPost::create(['country_id' => $country->id, 'title' => 'Old Driver', 'slug' => 'old-driver', 'is_active' => true, 'deadline' => now()->subDays(3)->toDateString()]);
        JobPost::create(['country_id' => $country->id, 'title' => 'Hidden Cook', 'slug' => 'hidden-cook', 'is_active' => false]);
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Jobs\Index::class)
            ->set('state', 'open')->assertSee('Open Welder')->assertDontSee('Old Driver')->assertDontSee('Hidden Cook')
            ->set('state', 'closed')->assertSee('Old Driver')->assertDontSee('Open Welder')
            ->set('state', 'inactive')->assertSee('Hidden Cook')->assertDontSee('Open Welder')
            ->set('state', '')->set('search', 'Driver')->assertSee('Old Driver')->assertDontSee('Hidden Cook');
    }

    public function test_admin_can_create_and_edit_a_country(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Countries\Form::class)
            ->set('name', 'United Arab Emirates')
            ->set('flag', '🇦🇪')
            ->set('job_sectors', "Construction\nHotels")
            ->set('sort_order', '3')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.countries.index'));

        $country = Country::firstWhere('name', 'United Arab Emirates');
        $this->assertSame('united-arab-emirates', $country->slug);
        $this->assertSame(3, $country->sort_order);

        Livewire::test(Countries\Form::class, ['country' => $country])
            ->set('summary', 'Updated summary')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Updated summary', $country->fresh()->summary);
    }

    public function test_country_slug_must_be_unique_and_valid(): void
    {
        $this->country();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Countries\Form::class)
            ->set('name', 'Another')
            ->set('slug', 'saudi-arabia')
            ->call('save')
            ->assertHasErrors(['slug']);

        Livewire::test(Countries\Form::class)
            ->set('name', 'Another')
            ->set('slug', 'Bad Slug!')
            ->call('save')
            ->assertHasErrors(['slug']);
    }

    public function test_country_with_jobs_cannot_be_deleted_but_empty_one_can(): void
    {
        $withJobs = $this->country();
        $this->job($withJobs);
        $empty = $this->country('Oman', 'oman');
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Countries\Index::class)
            ->call('confirmDelete', $withJobs->id)
            ->call('delete')
            ->assertDispatched('toast');
        $this->assertDatabaseHas('countries', ['id' => $withJobs->id]);

        Livewire::test(Countries\Index::class)
            ->call('confirmDelete', $empty->id)
            ->call('delete');
        $this->assertDatabaseMissing('countries', ['id' => $empty->id]);
    }

    public function test_toggle_hides_a_country(): void
    {
        $country = $this->country();
        $this->actingAs($this->userWithRole(RoleName::Admin));

        Livewire::test(Countries\Index::class)->call('toggleActive', $country->id);

        $this->assertFalse($country->fresh()->is_active);
    }
}
