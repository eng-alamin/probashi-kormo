<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Countries;

use App\Models\Country;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $countryId = null;

    public string $name = '';

    public string $slug = '';

    public string $flag = '';

    public string $summary = '';

    public string $description = '';

    public string $job_sectors = '';

    public string $contract_info = '';

    public string $extra_info = '';

    public string $sort_order = '0';

    public bool $is_active = true;

    public function mount(?Country $country = null): void
    {
        if ($country === null || ! $country->exists) {
            $this->authorize('countries.create');

            return;
        }

        $this->authorize('countries.update');

        $this->countryId = $country->id;
        $this->name = $country->name;
        $this->slug = $country->slug;
        $this->flag = (string) $country->flag;
        $this->summary = (string) $country->summary;
        $this->description = (string) $country->description;
        $this->job_sectors = (string) $country->job_sectors;
        $this->contract_info = (string) $country->contract_info;
        $this->extra_info = (string) $country->extra_info;
        $this->sort_order = (string) $country->sort_order;
        $this->is_active = $country->is_active;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('countries', 'slug')->ignore($this->countryId)],
            'flag' => ['nullable', 'string', 'max:16'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'job_sectors' => ['nullable', 'string', 'max:3000'],
            'contract_info' => ['nullable', 'string', 'max:255'],
            'extra_info' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'slug.regex' => 'Use only small letters, numbers and hyphens, for example saudi-arabia.',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->countryId === null ? 'countries.create' : 'countries.update');

        $this->name = trim($this->name);
        $this->slug = strtolower(trim($this->slug));

        $this->validate();

        $payload = [
            'name' => $this->name,
            'slug' => $this->slug !== '' ? $this->slug : Slug::unique(Country::class, $this->name, $this->countryId, 'country'),
            'flag' => Slug::blankToNull($this->flag),
            'summary' => Slug::blankToNull($this->summary),
            'description' => Slug::blankToNull($this->description),
            'job_sectors' => Slug::blankToNull($this->job_sectors),
            'contract_info' => Slug::blankToNull($this->contract_info),
            'extra_info' => Slug::blankToNull($this->extra_info),
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->countryId === null) {
            Country::create($payload);
            $message = 'Country created';
        } else {
            Country::findOrFail($this->countryId)->update($payload);
            $message = 'Country updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.countries.index');
    }

    public function render(): View
    {
        return view('livewire.admin.countries.form', [
            'isEdit' => $this->countryId !== null,
        ])->title($this->countryId === null ? 'New country' : 'Edit country');
    }
}
