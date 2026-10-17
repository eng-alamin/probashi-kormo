<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Settings;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
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

    /**
     * @return array<string, string>
     */
    private function valid(array $override = []): array
    {
        return array_merge([
            'phone' => '+880 1712-345678',
            'email' => 'info@probashikormo.test',
            'address' => 'House 12, Road 3, Dhaka',
            'whatsapp' => '+880 1712 345678',
            'hours' => 'Sat–Thu, 9am–6pm',
            'license_no' => 'RL-1234',
            'map_embed' => '',
            'home_title' => 'Build your future.',
            'home_highlight' => 'Work abroad, safely.',
            'home_text' => 'Honest recruitment from start to finish.',
            'stat1_value' => '700+', 'stat1_label' => 'Workers Sent',
            'stat2_value' => '8', 'stat2_label' => 'Countries',
            'stat3_value' => '12', 'stat3_label' => 'Trades',
            'stat4_value' => '100%', 'stat4_label' => 'Legal',
        ], $override);
    }

    private function save(array $override = []): \Livewire\Features\SupportTesting\Testable
    {
        $component = Livewire::test(Settings\Index::class);

        foreach ($this->valid($override) as $key => $value) {
            $component->set($key, $value);
        }

        return $component->call('save');
    }

    public function test_default_roles_and_menu(): void
    {
        $admin = $this->userWithRole(RoleName::Admin);
        $staff = $this->userWithRole(RoleName::Staff);

        $this->assertTrue($admin->can('settings.view'));
        $this->assertTrue($admin->can('settings.update'));
        $this->assertFalse($staff->can('settings.view'));

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertDontSee('nav-soon');
    }

    public function test_staff_cannot_open_settings_but_admin_can(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Staff))->get(route('admin.settings'))->assertForbidden();

        $this->actingAs($this->userWithRole(RoleName::Admin))
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Contact details')
            ->assertSee('Home page');
    }

    public function test_saved_contact_details_replace_the_env_values_on_the_site(): void
    {
        config(['site.phone' => '+880 1999-000111', 'site.email' => 'old@example.com']);
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save()->assertHasNoErrors()->assertRedirect(route('admin.settings'));

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('+880 1712-345678')
            ->assertSee('info@probashikormo.test')
            ->assertSee('Sat–Thu, 9am–6pm')
            ->assertDontSee('old@example.com');

        $this->get(route('about'))->assertSee('RL-1234');
        $this->get(route('home'))->assertSee('https://wa.me/8801712345678', false);
    }

    public function test_home_page_uses_the_saved_heading_text_and_numbers(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save()->assertHasNoErrors();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Build your future.')
            ->assertSee('Work abroad, safely.')
            ->assertSee('Honest recruitment from start to finish.')
            ->assertSee('700+')
            ->assertDontSee('Your Future Abroad.');
    }

    public function test_untouched_site_still_shows_the_default_home_text(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Your Skills.')
            ->assertSee('Your Future Abroad.')
            ->assertSee('500+');
    }

    public function test_the_red_part_of_the_heading_can_be_emptied_on_purpose(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save(['home_highlight' => ''])->assertHasNoErrors();

        $this->get(route('home'))->assertOk()->assertDontSee('Your Future Abroad.');
    }

    public function test_a_blank_contact_box_goes_back_to_the_env_value(): void
    {
        config(['site.phone' => '+880 1999-000111']);
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save()->assertHasNoErrors();
        $this->assertSame('+880 1712-345678', SiteSetting::where('key', 'phone')->value('value'));

        $this->save(['phone' => ''])->assertHasNoErrors();

        $this->assertDatabaseMissing('site_settings', ['key' => 'phone']);
        $this->get(route('contact'))->assertSee('+880 1999-000111');
    }

    public function test_whatsapp_is_saved_as_digits_only(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save()->assertHasNoErrors();

        $this->assertSame('8801712345678', SiteSetting::where('key', 'whatsapp')->value('value'));
    }

    public function test_validation(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save([
            'email' => 'not-an-email',
            'phone' => 'call me',
            'whatsapp' => '12',
            'map_embed' => 'https://evil.example.com/map',
            'home_title' => '',
            'home_text' => '',
            'stat1_value' => '',
            'stat4_label' => str_repeat('x', 41),
        ])->assertHasErrors(['email', 'phone', 'whatsapp', 'map_embed', 'home_title', 'home_text', 'stat1_value', 'stat4_label']);

        $this->assertSame(0, SiteSetting::count());
    }

    public function test_only_google_maps_embed_links_are_accepted(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));
        $link = 'https://www.google.com/maps/embed?pb=!1m18';

        $this->save(['map_embed' => $link])->assertHasNoErrors();

        $this->get(route('contact'))->assertOk()->assertSee($link, false);
    }

    public function test_a_user_who_can_only_view_cannot_save(): void
    {
        $role = \Spatie\Permission\Models\Role::findByName(RoleName::Staff->value, 'web');
        $role->givePermissionTo('settings.view');
        $this->actingAs($this->userWithRole(RoleName::Staff));

        $this->get(route('admin.settings'))->assertOk()->assertDontSee('Save settings');

        Livewire::test(Settings\Index::class)->set('home_title', 'Hacked')->call('save')->assertForbidden();
        $this->assertSame(0, SiteSetting::count());
    }

    public function test_saving_is_logged_without_the_values(): void
    {
        $this->actingAs($this->userWithRole(RoleName::Admin));

        $this->save()->assertHasNoErrors();

        $log = ActivityLog::where('subject_type', 'settings')->firstOrFail();
        $this->assertSame('Updated site settings', $log->description);
        $this->assertStringNotContainsString('1712', $log->description);
    }

    public function test_site_works_before_the_settings_table_exists(): void
    {
        \Illuminate\Support\Facades\Schema::drop('site_settings');
        \App\Support\Settings::flush();

        $this->get(route('home'))->assertOk()->assertSee('Your Skills.');
    }
}
