<?php

namespace Tests\Feature\Public;

use App\Models\Country;
use App\Models\GalleryItem;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\SuccessStory;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesPartTwoTest extends TestCase
{
    use RefreshDatabase;

    private function job(array $attrs = []): JobPost
    {
        $this->seed(CountrySeeder::class);

        return JobPost::create(array_merge([
            'country_id' => Country::first()->id,
            'title' => 'Test Electrician',
            'slug' => 'test-electrician-'.uniqid(),
            'summary' => 'A test job',
            'requirements' => "Age 22-35\nValid passport",
            'is_active' => true,
        ], $attrs));
    }

    public function test_static_pages_load_without_placeholders(): void
    {
        foreach (['about', 'terms', 'privacy-policy'] as $name) {
            $this->get(route($name))->assertOk()->assertDontSee('XXXX')->assertDontSee('add here');
        }
    }

    public function test_license_number_shows_only_when_configured(): void
    {
        $this->get(route('about'))->assertOk()->assertDontSee('Recruiting License No');

        config(['site.license_no' => 'RL-1234']);

        $this->get(route('about'))->assertOk()->assertSee('RL-1234');
    }

    public function test_jobs_empty_state_and_detail_pages(): void
    {
        $this->get(route('jobs'))->assertOk()->assertSee('No openings listed right now');

        $job = $this->job(['vacancies' => 5]);

        $this->get(route('jobs'))->assertOk()->assertSee('Test Electrician')->assertDontSee('No openings listed');
        $this->get(route('jobs.show', $job))->assertOk()->assertSee('Valid passport')->assertSee('As per contract');
    }

    public function test_salary_is_formatted_when_present(): void
    {
        $job = $this->job(['salary_min' => 1000, 'salary_max' => 1500, 'salary_currency' => 'SAR']);

        $this->get(route('jobs.show', $job))->assertOk()->assertSee('SAR 1,000 – 1,500 / month');
    }

    public function test_closed_job_is_hidden_from_list_but_detail_says_closed(): void
    {
        $job = $this->job(['deadline' => today()->subDay()]);

        $this->get(route('jobs'))->assertOk()->assertDontSee(route('jobs.show', $job));
        $this->get(route('jobs.show', $job))->assertOk()->assertSee('This opening is closed')->assertDontSee('Apply for This Job');
    }

    public function test_inactive_job_is_404(): void
    {
        $job = $this->job(['is_active' => false]);

        $this->get(route('jobs.show', $job))->assertNotFound();
    }

    public function test_gallery_lists_only_active_items(): void
    {
        GalleryItem::create(['title' => 'Visible photo', 'image_path' => 'gallery/a.jpg', 'category' => 'office', 'is_active' => true]);
        GalleryItem::create(['title' => 'Hidden photo', 'image_path' => 'gallery/b.jpg', 'category' => 'office', 'is_active' => false]);

        $this->get(route('gallery'))->assertOk()->assertSee('Visible photo')->assertDontSee('Hidden photo');
    }

    public function test_success_stories_show_published_only(): void
    {
        $this->get(route('success-stories'))->assertOk()->assertSee('Stories coming soon');

        SuccessStory::create(['name' => 'Rahim', 'trade' => 'Welder', 'story' => 'Real story text', 'is_published' => true]);
        SuccessStory::create(['name' => 'Draft Person', 'story' => 'Not ready', 'is_published' => false]);

        $this->get(route('success-stories'))->assertOk()->assertSee('Rahim')->assertDontSee('Draft Person');
    }

    public function test_news_respects_published_state_and_date(): void
    {
        $live = NewsPost::create(['title' => 'Live notice', 'slug' => 'live-notice', 'body' => "First para\n\nSecond para", 'is_published' => true, 'published_at' => now()->subHour()]);
        $draft = NewsPost::create(['title' => 'Draft notice', 'slug' => 'draft-notice', 'body' => 'x', 'is_published' => false, 'published_at' => now()->subHour()]);
        $future = NewsPost::create(['title' => 'Future notice', 'slug' => 'future-notice', 'body' => 'x', 'is_published' => true, 'published_at' => now()->addDay()]);

        $this->get(route('news'))->assertOk()->assertSee('Live notice')->assertDontSee('Draft notice')->assertDontSee('Future notice');
        $this->get(route('news.show', $live))->assertOk()->assertSee('Second para');
        $this->get(route('news.show', $draft))->assertNotFound();
        $this->get(route('news.show', $future))->assertNotFound();
    }

    public function test_news_body_is_escaped(): void
    {
        $post = NewsPost::create(['title' => 'Safe', 'slug' => 'safe', 'body' => '<script>alert(1)</script>', 'is_published' => true, 'published_at' => now()->subMinute()]);

        $this->get(route('news.show', $post))->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }
}
