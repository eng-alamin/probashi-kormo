<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Trainings;

use App\Models\TrainingCourse;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Training')]
class Index extends Component
{
    #[Url(except: '')]
    public string $search = '';

    public ?int $deletingId = null;

    public function toggleActive(int $id): void
    {
        $this->authorize('trainings.update');

        $course = TrainingCourse::findOrFail($id);
        $course->update(['is_active' => ! $course->is_active]);
        $this->dispatch('toast', message: $course->is_active ? 'Course is now visible on the website' : 'Course hidden from the website', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('trainings.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('trainings.delete');

        if ($this->deletingId !== null) {
            TrainingCourse::find($this->deletingId)?->delete();
            $this->dispatch('toast', message: 'Course deleted', type: 'ok');
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('trainings.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $courses = TrainingCourse::query()
            ->when($term !== '', fn ($q) => $q->where('title', 'like', $like))
            ->ordered()
            ->get();

        return view('livewire.admin.trainings.index', ['courses' => $courses]);
    }
}
