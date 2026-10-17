<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $serviceId = null;

    public string $title = '';

    public string $slug = '';

    public string $icon = '';

    public string $summary = '';

    public string $description = '';

    public string $what_we_do = '';

    public string $what_to_bring = '';

    public string $sort_order = '0';

    public bool $is_active = true;

    public function mount(?Service $service = null): void
    {
        if ($service === null || ! $service->exists) {
            $this->authorize('services.create');

            return;
        }

        $this->authorize('services.update');

        $this->serviceId = $service->id;
        $this->title = $service->title;
        $this->slug = $service->slug;
        $this->icon = (string) $service->icon;
        $this->summary = (string) $service->summary;
        $this->description = (string) $service->description;
        $this->what_we_do = (string) $service->what_we_do;
        $this->what_to_bring = (string) $service->what_to_bring;
        $this->sort_order = (string) $service->sort_order;
        $this->is_active = $service->is_active;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('services', 'slug')->ignore($this->serviceId)],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'what_we_do' => ['nullable', 'string', 'max:5000'],
            'what_to_bring' => ['nullable', 'string', 'max:5000'],
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
            'slug.regex' => 'Use only small letters, numbers and hyphens, for example visa-processing.',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->serviceId === null ? 'services.create' : 'services.update');

        $this->title = trim($this->title);
        $this->slug = strtolower(trim($this->slug));

        $this->validate();

        $payload = [
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : Slug::unique(Service::class, $this->title, $this->serviceId, 'service'),
            'icon' => Slug::blankToNull($this->icon),
            'summary' => Slug::blankToNull($this->summary),
            'description' => Slug::blankToNull($this->description),
            'what_we_do' => Slug::blankToNull($this->what_we_do),
            'what_to_bring' => Slug::blankToNull($this->what_to_bring),
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->serviceId === null) {
            Service::create($payload);
            $message = 'Service created';
        } else {
            Service::findOrFail($this->serviceId)->update($payload);
            $message = 'Service updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.services.index');
    }

    public function render(): View
    {
        return view('livewire.admin.services.form', [
            'isEdit' => $this->serviceId !== null,
        ])->title($this->serviceId === null ? 'New service' : 'Edit service');
    }
}
