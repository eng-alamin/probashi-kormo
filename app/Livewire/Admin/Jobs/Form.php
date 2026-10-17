<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Jobs;

use App\Models\Country;
use App\Models\JobPost;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $jobId = null;

    public string $country_id = '';

    public string $title = '';

    public string $slug = '';

    public string $category = '';

    public string $summary = '';

    public string $description = '';

    public string $requirements = '';

    public string $benefits = '';

    public string $salary_min = '';

    public string $salary_max = '';

    public string $salary_currency = '';

    public string $vacancies = '';

    public string $deadline = '';

    public bool $is_featured = false;

    public bool $is_active = true;

    public function mount(?JobPost $job = null): void
    {
        if ($job === null || ! $job->exists) {
            $this->authorize('jobs.create');

            return;
        }

        $this->authorize('jobs.update');

        $this->jobId = $job->id;
        $this->country_id = (string) $job->country_id;
        $this->title = $job->title;
        $this->slug = $job->slug;
        $this->category = (string) $job->category;
        $this->summary = (string) $job->summary;
        $this->description = (string) $job->description;
        $this->requirements = (string) $job->requirements;
        $this->benefits = (string) $job->benefits;
        $this->salary_min = (string) $job->salary_min;
        $this->salary_max = (string) $job->salary_max;
        $this->salary_currency = (string) $job->salary_currency;
        $this->vacancies = (string) $job->vacancies;
        $this->deadline = $job->deadline?->format('Y-m-d') ?? '';
        $this->is_featured = (bool) $job->is_featured;
        $this->is_active = (bool) ($job->is_active ?? true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('job_posts', 'slug')->ignore($this->jobId)],
            'category' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'benefits' => ['nullable', 'string', 'max:5000'],
            'salary_min' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'salary_max' => array_values(array_filter([
                'nullable', 'integer', 'min:0', 'max:100000000',
                $this->salary_min !== '' ? 'gte:salary_min' : null,
            ])),
            'salary_currency' => ['nullable', 'regex:/^[A-Z]{3}$/'],
            'vacancies' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'deadline' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'slug.regex' => 'Use only small letters, numbers and hyphens, for example electrician-saudi-arabia.',
            'salary_currency.regex' => 'Use a 3-letter code such as SAR, AED or MYR.',
            'salary_max.gte' => 'Maximum salary cannot be lower than minimum salary.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'country_id' => 'country',
            'salary_min' => 'minimum salary',
            'salary_max' => 'maximum salary',
            'salary_currency' => 'currency',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->jobId === null ? 'jobs.create' : 'jobs.update');

        $this->title = trim($this->title);
        $this->slug = strtolower(trim($this->slug));
        $this->salary_currency = strtoupper(trim($this->salary_currency));

        $this->validate();

        $payload = [
            'country_id' => (int) $this->country_id,
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : Slug::unique(JobPost::class, $this->title, $this->jobId, 'job'),
            'category' => Slug::blankToNull($this->category),
            'summary' => Slug::blankToNull($this->summary),
            'description' => Slug::blankToNull($this->description),
            'requirements' => Slug::blankToNull($this->requirements),
            'benefits' => Slug::blankToNull($this->benefits),
            'salary_min' => $this->salary_min !== '' ? (int) $this->salary_min : null,
            'salary_max' => $this->salary_max !== '' ? (int) $this->salary_max : null,
            'salary_currency' => Slug::blankToNull($this->salary_currency),
            'vacancies' => $this->vacancies !== '' ? (int) $this->vacancies : null,
            'deadline' => Slug::blankToNull($this->deadline),
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
        ];

        if ($this->jobId === null) {
            JobPost::create($payload);
            $message = 'Job created';
        } else {
            JobPost::findOrFail($this->jobId)->update($payload);
            $message = 'Job updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.jobs.index');
    }

    public function render(): View
    {
        return view('livewire.admin.jobs.form', [
            'countries' => Country::ordered()->get(['id', 'name']),
            'isEdit' => $this->jobId !== null,
        ])->title($this->jobId === null ? 'New job' : 'Edit job');
    }
}
