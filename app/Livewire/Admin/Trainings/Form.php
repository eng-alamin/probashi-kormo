<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Trainings;

use App\Models\TrainingCourse;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $courseId = null;

    public string $title = '';

    public string $slug = '';

    public string $icon = '';

    public string $summary = '';

    public string $description = '';

    public string $learn_points = '';

    public string $career_points = '';

    public string $eligibility = '';

    public string $duration_weeks = '';

    public string $fee = '';

    public string $class_days = '';

    public string $certificate_info = '';

    public string $sort_order = '0';

    public bool $is_active = true;

    public function mount(?TrainingCourse $course = null): void
    {
        if ($course === null || ! $course->exists) {
            $this->authorize('trainings.create');

            return;
        }

        $this->authorize('trainings.update');

        $this->courseId = $course->id;
        $this->title = $course->title;
        $this->slug = $course->slug;
        $this->icon = (string) $course->icon;
        $this->summary = (string) $course->summary;
        $this->description = (string) $course->description;
        $this->learn_points = (string) $course->learn_points;
        $this->career_points = (string) $course->career_points;
        $this->eligibility = (string) $course->eligibility;
        $this->duration_weeks = (string) $course->duration_weeks;
        $this->fee = (string) $course->fee;
        $this->class_days = (string) $course->class_days;
        $this->certificate_info = (string) $course->certificate_info;
        $this->sort_order = (string) $course->sort_order;
        $this->is_active = $course->is_active;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('training_courses', 'slug')->ignore($this->courseId)],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'learn_points' => ['nullable', 'string', 'max:5000'],
            'career_points' => ['nullable', 'string', 'max:5000'],
            'eligibility' => ['nullable', 'string', 'max:5000'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:520'],
            'fee' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'class_days' => ['nullable', 'string', 'max:100'],
            'certificate_info' => ['nullable', 'string', 'max:255'],
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
            'slug.regex' => 'Use only small letters, numbers and hyphens, for example electrician.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'duration_weeks' => 'duration',
            'fee' => 'course fee',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->courseId === null ? 'trainings.create' : 'trainings.update');

        $this->title = trim($this->title);
        $this->slug = strtolower(trim($this->slug));

        $this->validate();

        $payload = [
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : Slug::unique(TrainingCourse::class, $this->title, $this->courseId, 'course'),
            'icon' => Slug::blankToNull($this->icon),
            'summary' => Slug::blankToNull($this->summary),
            'description' => Slug::blankToNull($this->description),
            'learn_points' => Slug::blankToNull($this->learn_points),
            'career_points' => Slug::blankToNull($this->career_points),
            'eligibility' => Slug::blankToNull($this->eligibility),
            'duration_weeks' => $this->duration_weeks !== '' ? (int) $this->duration_weeks : null,
            'fee' => $this->fee !== '' ? (int) $this->fee : null,
            'class_days' => Slug::blankToNull($this->class_days),
            'certificate_info' => Slug::blankToNull($this->certificate_info),
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->courseId === null) {
            TrainingCourse::create($payload);
            $message = 'Course created';
        } else {
            TrainingCourse::findOrFail($this->courseId)->update($payload);
            $message = 'Course updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.trainings.index');
    }

    public function render(): View
    {
        return view('livewire.admin.trainings.form', [
            'isEdit' => $this->courseId !== null,
        ])->title($this->courseId === null ? 'New course' : 'Edit course');
    }
}
