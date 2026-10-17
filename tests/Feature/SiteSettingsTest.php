<?php

namespace Tests\Feature;

use App\Support\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    // The home page reads services, countries and courses, so it needs the tables.
    use RefreshDatabase;

    private function setContact(?string $phone, ?string $email, ?string $address, ?string $whatsapp): void
    {
        config(['site.phone' => $phone, 'site.email' => $email, 'site.address' => $address, 'site.whatsapp' => $whatsapp]);
    }

    public function test_nothing_unfilled_or_placeholder_is_shown_publicly(): void
    {
        $this->setContact('+880 1XXX-XXXXXX', 'not-an-email', 'Your office address, Bangladesh', '8801XXXXXXXXX');

        $page = $this->get('/')->assertOk();
        $page->assertDontSee('1XXX-XXXXXX')->assertDontSee('Your office address')->assertDontSee('wa.me')->assertDontSee('mailto:')->assertDontSee('tel:');

        $this->get(route('contact'))->assertOk()->assertDontSee('1XXX-XXXXXX')->assertDontSee('Your office address');
    }

    public function test_filled_details_are_shown_with_working_links(): void
    {
        $this->setContact('+880 1712-345678', 'info@example.com', 'House 1, Road 2, Dhaka', '+880 1712-345678');

        $this->get('/')
            ->assertOk()
            ->assertSee('tel:+8801712345678', false)
            ->assertSee('mailto:info@example.com', false)
            ->assertSee('House 1, Road 2, Dhaka')
            ->assertSee('https://wa.me/8801712345678', false);
    }

    public function test_whatsapp_must_be_a_plausible_international_number(): void
    {
        config(['site.whatsapp' => '01712345678']); // leading 0, no country code
        $this->assertNull(Site::whatsapp());

        config(['site.whatsapp' => 'abc']);
        $this->assertNull(Site::whatsapp());

        config(['site.whatsapp' => '+880 (1712) 345-678']);
        $this->assertSame('8801712345678', Site::whatsapp());
    }

    public function test_site_check_command_fails_until_required_details_exist(): void
    {
        $this->setContact(null, null, null, null);
        $this->artisan('site:check')->assertFailed();

        $this->setContact('01712345678', 'info@example.com', 'Dhaka', '8801712345678');
        $this->artisan('site:check')->assertSuccessful();
    }
}
