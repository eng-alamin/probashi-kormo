<?php

namespace Tests\Feature\Database;

use App\Enums\ApplicationStatus;
use App\Enums\PassportStatus;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSchemaTest extends TestCase
{
    use RefreshDatabase;

    private function makeCountry(string $slug = 'saudi-arabia'): Country
    {
        return Country::create(['name' => ucfirst($slug), 'slug' => $slug]);
    }

    private function makeJob(Country $country, array $overrides = []): JobPost
    {
        return JobPost::create(array_merge([
            'country_id' => $country->id,
            'title' => 'Welder',
            'slug' => 'welder-'.uniqid(),
        ], $overrides));
    }

    public function test_job_post_belongs_to_country(): void
    {
        $country = $this->makeCountry();
        $job = $this->makeJob($country);

        $this->assertTrue($job->country->is($country));
        $this->assertCount(1, $country->jobPosts);
    }

    public function test_country_with_jobs_cannot_be_deleted(): void
    {
        $country = $this->makeCountry();
        $this->makeJob($country);

        $this->expectException(QueryException::class);

        $country->delete();
    }

    public function test_deleting_a_job_keeps_the_application(): void
    {
        $country = $this->makeCountry();
        $job = $this->makeJob($country);

        $application = JobApplication::create([
            'job_post_id' => $job->id,
            'country_id' => $country->id,
            'full_name' => 'Test Applicant',
            'phone' => '01700000000',
        ]);

        $job->delete();

        $this->assertNull($application->fresh()->job_post_id);
        $this->assertSame('Test Applicant', $application->fresh()->full_name);
    }

    public function test_application_has_default_status_and_passport(): void
    {
        $application = JobApplication::create([
            'full_name' => 'Test Applicant',
            'phone' => '01700000000',
        ])->refresh();

        $this->assertSame(ApplicationStatus::New, $application->status);
        $this->assertSame(PassportStatus::None, $application->passport_status);
    }

    public function test_application_handler_is_cleared_when_user_is_deleted(): void
    {
        $user = User::factory()->create();

        $application = JobApplication::create([
            'full_name' => 'Test Applicant',
            'phone' => '01700000000',
            'handled_by' => $user->id,
        ]);

        $this->assertTrue($application->handledBy->is($user));

        $user->delete();

        $this->assertNull($application->fresh()->handled_by);
    }

    public function test_inquiry_can_be_saved_without_country(): void
    {
        $inquiry = Inquiry::create([
            'full_name' => 'Test Person',
            'phone' => '01800000000',
            'message' => 'Hello',
        ])->refresh();

        $this->assertNull($inquiry->country_id);
        $this->assertSame('new', $inquiry->status->value);
    }

    public function test_open_scope_hides_inactive_and_expired_jobs(): void
    {
        $country = $this->makeCountry();

        $openNoDeadline = $this->makeJob($country, ['slug' => 'open-no-deadline']);
        $openFuture = $this->makeJob($country, ['slug' => 'open-future', 'deadline' => now()->addWeek()->toDateString()]);
        $this->makeJob($country, ['slug' => 'expired', 'deadline' => now()->subDay()->toDateString()]);
        $this->makeJob($country, ['slug' => 'inactive', 'is_active' => false]);

        $slugs = JobPost::open()->pluck('slug')->all();

        $this->assertEqualsCanonicalizing([$openNoDeadline->slug, $openFuture->slug], $slugs);
    }

    public function test_published_scope_hides_drafts_and_future_posts(): void
    {
        NewsPost::create(['title' => 'Live', 'slug' => 'live', 'body' => 'x', 'is_published' => true, 'published_at' => now()->subHour()]);
        NewsPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'x', 'is_published' => false, 'published_at' => now()->subHour()]);
        NewsPost::create(['title' => 'Future', 'slug' => 'future', 'body' => 'x', 'is_published' => true, 'published_at' => now()->addDay()]);

        $this->assertSame(['live'], NewsPost::published()->pluck('slug')->all());
    }

    public function test_success_story_survives_country_deletion(): void
    {
        $country = $this->makeCountry('malaysia');

        $story = SuccessStory::create([
            'country_id' => $country->id,
            'name' => 'Test Worker',
            'story' => 'Went abroad and did well.',
        ]);

        $country->delete();

        $this->assertNull($story->fresh()->country_id);
    }

    public function test_slug_must_be_unique(): void
    {
        $this->makeCountry('qatar');

        $this->expectException(QueryException::class);

        $this->makeCountry('qatar');
    }
}
