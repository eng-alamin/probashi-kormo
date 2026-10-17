<?php

namespace Tests\Feature\Public;

use App\Models\Country;
use App\Models\Service;
use App\Models\TrainingCourse;
use App\Support\Lines;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\TrainingCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CountrySeeder::class);
        $this->seed(ServiceSeeder::class);
        $this->seed(TrainingCourseSeeder::class);
    }

    public function test_home_page_lists_seeded_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(Service::first()->title)
            ->assertSee(Country::first()->name)
            ->assertSee(TrainingCourse::first()->title);
    }

    public function test_list_pages_load(): void
    {
        $this->get(route('services'))->assertOk()->assertSee(Service::first()->title);
        $this->get(route('countries'))->assertOk()->assertSee(Country::first()->name);
        $this->get(route('training'))->assertOk()->assertSee(TrainingCourse::first()->title);
    }

    public function test_every_active_detail_page_loads(): void
    {
        foreach (Service::all() as $service) {
            $this->get(route('services.show', $service))->assertOk()->assertSee($service->title);
        }
        foreach (Country::all() as $country) {
            $this->get(route('countries.show', $country))->assertOk()->assertSee($country->name);
        }
        foreach (TrainingCourse::all() as $course) {
            $this->get(route('training.show', $course))->assertOk()->assertSee($course->title);
        }
    }

    public function test_inactive_items_return_404_and_disappear_from_lists(): void
    {
        $service = Service::first();
        $country = Country::first();
        $course = TrainingCourse::first();

        $service->update(['is_active' => false]);
        $country->update(['is_active' => false]);
        $course->update(['is_active' => false]);

        $this->get(route('services.show', $service))->assertNotFound();
        $this->get(route('countries.show', $country))->assertNotFound();
        $this->get(route('training.show', $course))->assertNotFound();

        $this->get(route('services'))->assertOk()->assertDontSee(route('services.show', $service));
        $this->get(route('countries'))->assertOk()->assertDontSee(route('countries.show', $country));
        $this->get(route('training'))->assertOk()->assertDontSee(route('training.show', $course));
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/services/does-not-exist')->assertNotFound();
        $this->get('/countries/does-not-exist')->assertNotFound();
        $this->get('/training/does-not-exist')->assertNotFound();
    }

    public function test_missing_course_details_show_contact_us_not_blank(): void
    {
        $course = TrainingCourse::first();
        $course->update(['duration_weeks' => null, 'fee' => null, 'class_days' => null, 'certificate_info' => null]);

        $this->get(route('training.show', $course))->assertOk()->assertSee('Contact us');
    }

    public function test_lines_helper_splits_and_cleans_text(): void
    {
        $this->assertSame([], Lines::split(null));
        $this->assertSame([], Lines::split("  \n "));
        $this->assertSame(['A', 'B', 'C'], Lines::split("A\r\n\n  B \nC"));
    }
}
