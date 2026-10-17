<?php

namespace Tests\Feature\Database;

use App\Models\GalleryItem;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\SuccessStory;
use Database\Seeders\CountrySeeder;
use Database\Seeders\GalleryItemSeeder;
use Database\Seeders\JobPostSeeder;
use Database\Seeders\NewsPostSeeder;
use Database\Seeders\SuccessStorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteContentSeedTest extends TestCase
{
    use RefreshDatabase;

    private function seedAll(): void
    {
        Storage::fake('public');
        $this->seed([CountrySeeder::class, JobPostSeeder::class, NewsPostSeeder::class, SuccessStorySeeder::class, GalleryItemSeeder::class]);
    }

    public function test_seeders_create_public_content_linked_to_countries(): void
    {
        $this->seedAll();

        $this->assertSame(7, JobPost::open()->count());
        $this->assertSame(0, JobPost::whereNull('country_id')->count());
        $this->assertSame(6, NewsPost::published()->count());
        $this->assertSame(5, SuccessStory::where('is_published', true)->count());
        $this->assertSame(6, GalleryItem::where('is_active', true)->count());
        $this->assertSame(2, GalleryItem::where('category', 'departure')->count());
    }

    public function test_gallery_placeholders_are_written_to_the_public_disk(): void
    {
        $this->seedAll();

        foreach (GalleryItem::all() as $item) {
            Storage::disk('public')->assertExists($item->image_path);
        }
    }

    public function test_running_again_keeps_admin_edits_and_adds_no_duplicates(): void
    {
        $this->seedAll();

        JobPost::where('slug', 'electrician-saudi-arabia')->update(['title' => 'Edited by admin']);
        NewsPost::where('slug', 'apply-online')->update(['is_published' => false]);

        $this->seed([JobPostSeeder::class, NewsPostSeeder::class, SuccessStorySeeder::class, GalleryItemSeeder::class]);

        $this->assertSame('Edited by admin', JobPost::where('slug', 'electrician-saudi-arabia')->value('title'));
        $this->assertFalse((bool) NewsPost::where('slug', 'apply-online')->value('is_published'));
        $this->assertSame(7, JobPost::count());
        $this->assertSame(6, NewsPost::count());
        $this->assertSame(5, SuccessStory::count());
        $this->assertSame(6, GalleryItem::count());
    }

    public function test_seeded_content_shows_on_the_public_pages(): void
    {
        $this->seedAll();

        $this->get(route('jobs'))->assertOk()->assertSee('Electrician')->assertSee('Welder (Arc / MIG)');
        $this->get(route('jobs.show', 'electrician-saudi-arabia'))->assertOk()->assertSee('Valid passport');
        $this->get(route('news'))->assertOk()->assertSee('How to Avoid Overseas Job Fraud');
        $this->get(route('news.show', 'avoid-overseas-job-fraud'))->assertOk();
        $this->get(route('success-stories'))->assertOk()->assertSee('Rafiq');
        $this->get(route('gallery'))->assertOk()->assertSee('Hands-on Training');
    }

    public function test_site_loads_the_glass_stylesheet(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('site/css/glass.css', false);
        $this->assertFileExists(public_path('site/css/glass.css'));
    }

    public function test_admin_screens_start_in_light_theme(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee("|| 'light'", false)
            ->assertDontSee("|| 'dark'", false);
    }
}
