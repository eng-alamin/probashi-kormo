<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\Service;
use App\Models\TrainingCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function country(array $attrs = []): Country
    {
        return Country::create(array_merge([
            'name' => 'Testland',
            'slug' => 'testland-'.uniqid(),
            'is_active' => true,
        ], $attrs));
    }

    private function job(array $attrs = []): JobPost
    {
        return JobPost::create(array_merge([
            'country_id' => $this->country()->id,
            'title' => 'Test Welder',
            'slug' => 'test-welder-'.uniqid(),
            'summary' => 'A test job',
            'is_active' => true,
        ], $attrs));
    }

    public function test_sitemap_is_xml_and_lists_static_pages(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('jobs'), false)
            ->assertSee(route('contact'), false);
    }

    public function test_sitemap_lists_only_public_items(): void
    {
        $country = $this->country(['slug' => 'visible-land']);
        $this->country(['slug' => 'hidden-land', 'is_active' => false]);
        Service::create(['title' => 'S1', 'slug' => 'visible-service', 'is_active' => true]);
        Service::create(['title' => 'S2', 'slug' => 'hidden-service', 'is_active' => false]);
        TrainingCourse::create(['title' => 'C1', 'slug' => 'visible-course', 'is_active' => true]);
        TrainingCourse::create(['title' => 'C2', 'slug' => 'hidden-course', 'is_active' => false]);
        $this->job(['slug' => 'open-job', 'country_id' => $country->id]);
        $this->job(['slug' => 'expired-job', 'deadline' => today()->subDay()]);
        $this->job(['slug' => 'inactive-job', 'is_active' => false]);
        NewsPost::create(['title' => 'N1', 'slug' => 'live-news', 'body' => 'x', 'is_published' => true, 'published_at' => now()->subDay()]);
        NewsPost::create(['title' => 'N2', 'slug' => 'draft-news', 'body' => 'x', 'is_published' => false]);
        NewsPost::create(['title' => 'N3', 'slug' => 'future-news', 'body' => 'x', 'is_published' => true, 'published_at' => now()->addDay()]);

        $response = $this->get('/sitemap.xml')->assertOk();

        foreach (['visible-land', 'visible-service', 'visible-course', 'open-job', 'live-news'] as $slug) {
            $response->assertSee($slug, false);
        }

        foreach (['hidden-land', 'hidden-service', 'hidden-course', 'expired-job', 'inactive-job', 'draft-news', 'future-news'] as $slug) {
            $response->assertDontSee($slug, false);
        }
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /', false)
            ->assertDontSee('Sitemap:', false);
    }

    public function test_robots_in_production_blocks_admin_and_points_to_sitemap(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: '.route('sitemap'), false);
    }

    public function test_pages_have_canonical_and_open_graph_tags(): void
    {
        $this->get(route('about').'?utm_source=test')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('about').'">', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('name="twitter:card"', false);
    }

    public function test_home_has_organization_structured_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('EmploymentAgency', false);
    }

    public function test_open_job_has_job_posting_structured_data(): void
    {
        $job = $this->job(['deadline' => today()->addWeek(), 'salary_min' => 500, 'salary_max' => 700, 'salary_currency' => 'SAR']);

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('"@type":"JobPosting"', false)
            ->assertSee('"validThrough":"'.today()->addWeek()->toDateString().'"', false)
            ->assertSee('"currency":"SAR"', false);
    }

    public function test_closed_job_has_no_job_posting_structured_data(): void
    {
        $job = $this->job(['deadline' => today()->subDay()]);

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertDontSee('JobPosting', false);
    }

    public function test_news_with_cover_uses_it_as_share_image(): void
    {
        $post = NewsPost::create([
            'title' => 'With cover',
            'slug' => 'with-cover',
            'body' => 'Body',
            'cover_image_path' => 'news/cover.jpg',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('news.show', $post))
            ->assertOk()
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/news/cover.jpg').'">', false);
    }

    public function test_structured_data_cannot_break_out_of_the_script_tag(): void
    {
        $job = $this->job(['title' => 'Evil </script><script>alert(1)</script>']);

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertDontSee('</script><script>alert(1)', false);
    }
}
