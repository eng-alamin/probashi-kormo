<?php

namespace Tests\Feature\Database;

use App\Enums\GalleryCategory;
use App\Models\Country;
use App\Models\GalleryItem;
use App\Models\Service;
use App\Models\TrainingCourse;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\TrainingCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSeedTest extends TestCase
{
    use RefreshDatabase;

    private function seedContent(): void
    {
        $this->seed(CountrySeeder::class);
        $this->seed(ServiceSeeder::class);
        $this->seed(TrainingCourseSeeder::class);
    }

    public function test_content_seeders_create_the_expected_rows(): void
    {
        $this->seedContent();

        $this->assertSame(7, Country::count());
        $this->assertSame(6, Service::count());
        $this->assertSame(8, TrainingCourse::count());
    }

    public function test_seeded_rows_have_their_page_content(): void
    {
        $this->seedContent();

        foreach (Country::all() as $country) {
            $this->assertNotEmpty($country->flag, $country->slug);
            $this->assertNotEmpty($country->job_sectors, $country->slug);
        }

        foreach (Service::all() as $service) {
            $this->assertNotEmpty($service->what_we_do, $service->slug);
        }

        foreach (TrainingCourse::all() as $course) {
            $this->assertNotEmpty($course->learn_points, $course->slug);
        }
    }

    public function test_running_the_seeders_again_keeps_admin_edits(): void
    {
        $this->seedContent();

        Country::where('slug', 'qatar')->update(['summary' => 'Edited by admin']);

        $this->seedContent();

        $this->assertSame(7, Country::count());
        $this->assertSame('Edited by admin', Country::where('slug', 'qatar')->value('summary'));
    }

    public function test_gallery_category_is_cast_to_enum(): void
    {
        $item = GalleryItem::create([
            'image_path' => 'gallery/a.jpg',
            'category' => 'training',
        ])->refresh();

        $this->assertSame(GalleryCategory::Training, $item->category);
    }
}
