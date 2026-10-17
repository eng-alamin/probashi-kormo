<?php

namespace Tests\Feature\Public;

use App\Enums\ApplicationStatus;
use App\Enums\InquiryStatus;
use App\Enums\PassportStatus;
use App\Livewire\Pages\Apply;
use App\Livewire\Pages\Contact;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CountrySeeder::class);
        RateLimiter::clear('apply:127.0.0.1');
        RateLimiter::clear('contact:127.0.0.1');
    }

    private function fillApply($component, array $override = [])
    {
        $data = array_merge([
            'full_name' => 'Rahim Uddin',
            'phone' => '01712 345-678',
            'age' => '27',
            'district' => 'Cumilla',
            'passport_status' => 'has',
            'education' => 'SSC',
            'country_id' => (string) Country::first()->id,
            'trade' => 'Electrician',
            'experience' => '2 years',
            'message' => 'Ready to join',
        ], $override);

        foreach ($data as $field => $value) {
            $component->set('form.'.$field, $value);
        }

        return $component;
    }

    public function test_pages_load(): void
    {
        $this->get(route('apply'))->assertOk()->assertSee('Job Application Form');
        $this->get(route('contact'))->assertOk()->assertSee('Send Us a Message');
    }

    public function test_valid_application_is_saved(): void
    {
        $component = $this->fillApply(Livewire::test(Apply::class))->call('submit');

        $component->assertHasNoErrors()->assertSet('sent', true);

        $app = JobApplication::first();
        $this->assertSame('Rahim Uddin', $app->full_name);
        $this->assertSame('01712345678', $app->phone);
        $this->assertSame(27, $app->age);
        $this->assertSame(PassportStatus::Has, $app->passport_status);
        $this->assertSame(ApplicationStatus::New, $app->status);
        $this->assertSame(Country::first()->id, $app->country_id);
        $this->assertNull($app->job_post_id);
        $this->assertStringStartsWith('PK-', $component->get('reference'));
    }

    public function test_other_country_is_stored_as_null(): void
    {
        $this->fillApply(Livewire::test(Apply::class), ['country_id' => 'other'])->call('submit')->assertHasNoErrors();

        $this->assertNull(JobApplication::first()->country_id);
    }

    public function test_required_fields_and_ranges_are_validated(): void
    {
        Livewire::test(Apply::class)
            ->call('submit')
            ->assertHasErrors(['form.full_name', 'form.phone', 'form.age', 'form.district', 'form.passport_status', 'form.country_id', 'form.trade']);

        $this->fillApply(Livewire::test(Apply::class), ['age' => '12', 'phone' => 'abc', 'passport_status' => 'maybe', 'country_id' => '99999'])
            ->call('submit')
            ->assertHasErrors(['form.age', 'form.phone', 'form.passport_status', 'form.country_id']);

        $this->assertSame(0, JobApplication::count());
    }

    public function test_job_link_prefills_and_links_the_application(): void
    {
        $country = Country::first();
        $job = JobPost::create(['country_id' => $country->id, 'title' => 'Welder', 'slug' => 'welder-x', 'is_active' => true]);

        $component = Livewire::withQueryParams(['job' => 'welder-x'])->test(Apply::class);
        $component->assertSet('form.trade', 'Welder')->assertSet('form.country_id', (string) $country->id);

        $component->call('submit');
        $component->assertHasErrors(['form.full_name']); // other fields still required

        $this->fillApply($component)->call('submit')->assertHasNoErrors();

        $this->assertSame($job->id, JobApplication::first()->job_post_id);
    }

    public function test_unknown_job_slug_is_ignored(): void
    {
        Livewire::withQueryParams(['job' => 'nope'])->test(Apply::class)->assertSet('form.trade', '');
    }

    public function test_same_phone_and_job_is_not_stored_twice(): void
    {
        $this->fillApply(Livewire::test(Apply::class))->call('submit');
        $this->fillApply(Livewire::test(Apply::class))->call('submit')->assertSet('sent', true);

        $this->assertSame(1, JobApplication::count());
    }

    public function test_honeypot_stores_nothing_but_looks_successful(): void
    {
        $this->fillApply(Livewire::test(Apply::class), ['website' => 'http://spam.example'])
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertSame(0, JobApplication::count());
    }

    public function test_application_rate_limit(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->fillApply(Livewire::test(Apply::class), ['phone' => '0171234567'.$i])->call('submit')->assertHasNoErrors();
        }

        $this->fillApply(Livewire::test(Apply::class), ['phone' => '01799999999'])->call('submit')->assertHasErrors(['form.phone']);

        $this->assertSame(5, JobApplication::count());
    }

    public function test_html_in_fields_is_stored_as_text_and_escaped_on_output(): void
    {
        $this->fillApply(Livewire::test(Apply::class), ['full_name' => '<b>Bold</b> Name'])->call('submit')->assertHasNoErrors();

        $this->assertSame('<b>Bold</b> Name', JobApplication::first()->full_name);
    }

    public function test_valid_message_is_saved(): void
    {
        Livewire::test(Contact::class)
            ->set('form.full_name', 'Karim')
            ->set('form.phone', '01812345678')
            ->set('form.country_id', 'other')
            ->set('form.trade', 'Driver')
            ->set('form.message', 'What is the cost?')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $inquiry = Inquiry::first();
        $this->assertSame('Karim', $inquiry->full_name);
        $this->assertSame(InquiryStatus::New, $inquiry->status);
        $this->assertNull($inquiry->country_id);
    }

    public function test_contact_requires_name_phone_country_and_message(): void
    {
        Livewire::test(Contact::class)
            ->call('submit')
            ->assertHasErrors(['form.full_name', 'form.phone', 'form.country_id', 'form.message']);

        $this->assertSame(0, Inquiry::count());
    }

    public function test_contact_honeypot_and_duplicate(): void
    {
        $send = fn (array $extra = []) => Livewire::test(Contact::class)
            ->set('form.full_name', 'Karim')
            ->set('form.phone', '01812345678')
            ->set('form.country_id', 'other')
            ->set('form.message', 'Same message')
            ->set('form.website', $extra['website'] ?? '')
            ->call('submit');

        $send(['website' => 'spam']);
        $this->assertSame(0, Inquiry::count());

        $send();
        $send();
        $this->assertSame(1, Inquiry::count());
    }

    public function test_map_and_hours_show_only_when_configured(): void
    {
        $this->get(route('contact'))->assertOk()->assertDontSee('Our Location')->assertDontSee('Office Hours');

        config(['site.hours' => 'Sat-Thu 9am-6pm', 'site.map_embed' => 'https://www.google.com/maps/embed?pb=abc']);
        $this->get(route('contact'))->assertOk()->assertSee('Our Location')->assertSee('Sat-Thu 9am-6pm');

        config(['site.map_embed' => 'https://evil.example/frame']);
        $this->get(route('contact'))->assertOk()->assertDontSee('evil.example');
    }
}
