<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Enums\ActivityEvent;
use App\Support\ActivityLogger;
use App\Support\Settings;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Index extends Component
{
    // Contact details
    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $whatsapp = '';

    public string $hours = '';

    public string $map_embed = '';

    public string $license_no = '';

    // Home page
    public string $home_title = '';

    public string $home_highlight = '';

    public string $home_text = '';

    public string $stat1_value = '';

    public string $stat1_label = '';

    public string $stat2_value = '';

    public string $stat2_label = '';

    public string $stat3_value = '';

    public string $stat3_label = '';

    public string $stat4_value = '';

    public string $stat4_label = '';

    public function mount(): void
    {
        $this->authorize('settings.view');

        $this->phone = (string) Site::phone();
        $this->email = (string) Site::email();
        $this->address = (string) Site::address();
        $this->whatsapp = (string) Site::whatsapp();
        $this->hours = (string) Site::hours();
        $this->map_embed = (string) Site::mapEmbed();
        $this->license_no = (string) Site::licenseNo();

        $home = Site::home();
        $this->home_title = $home['title'];
        $this->home_highlight = $home['highlight'];
        $this->home_text = $home['text'];

        foreach ($home['stats'] as $i => [$value, $label]) {
            $n = $i + 1;
            $this->{"stat{$n}_value"} = $value;
            $this->{"stat{$n}_label"} = $label;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{6,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:25', function (string $attribute, mixed $value, \Closure $fail): void {
                $digits = (string) preg_replace('/[\s+\-()]/', '', (string) $value);

                if ($digits !== '' && preg_match('/^[1-9][0-9]{7,14}$/', $digits) !== 1) {
                    $fail('Use the international number, country code first, for example 8801712345678.');
                }
            }],
            'hours' => ['nullable', 'string', 'max:100'],
            'map_embed' => ['nullable', 'string', 'max:1500', 'starts_with:https://www.google.com/maps/embed'],
            'license_no' => ['nullable', 'string', 'max:60'],

            'home_title' => ['required', 'string', 'max:60'],
            'home_highlight' => ['nullable', 'string', 'max:60'],
            'home_text' => ['required', 'string', 'max:300'],
            'stat1_value' => ['required', 'string', 'max:12'],
            'stat1_label' => ['required', 'string', 'max:40'],
            'stat2_value' => ['required', 'string', 'max:12'],
            'stat2_label' => ['required', 'string', 'max:40'],
            'stat3_value' => ['required', 'string', 'max:12'],
            'stat3_label' => ['required', 'string', 'max:40'],
            'stat4_value' => ['required', 'string', 'max:12'],
            'stat4_label' => ['required', 'string', 'max:40'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'phone.regex' => 'Use digits, spaces, + - and brackets only.',
            'map_embed.starts_with' => 'Paste only the src="..." link of a Google Maps embed (it starts with https://www.google.com/maps/embed).',
        ];
    }

    public function save(): void
    {
        $this->authorize('settings.update');

        $this->validate();

        $keys = [
            'phone', 'email', 'address', 'whatsapp', 'hours', 'map_embed', 'license_no',
            'home_title', 'home_highlight', 'home_text',
            'stat1_value', 'stat1_label', 'stat2_value', 'stat2_label',
            'stat3_value', 'stat3_label', 'stat4_value', 'stat4_label',
        ];

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = trim((string) $this->{$key});
        }

        // WhatsApp is kept as digits only, which is what wa.me needs.
        $values['whatsapp'] = (string) preg_replace('/[\s+\-()]/', '', $values['whatsapp']);

        Settings::set($values, keepBlank: ['home_highlight']);

        // Only the fact of the change is logged, never the values (phone numbers, etc.).
        ActivityLogger::record(ActivityEvent::Updated, 'Updated site settings', 'settings');

        session()->flash('toast', 'Settings saved');

        $this->redirectRoute('admin.settings');
    }

    public function render(): View
    {
        return view('livewire.admin.settings.index', [
            'canEdit' => auth()->user()?->can('settings.update') === true,
        ])->title('Settings');
    }
}
