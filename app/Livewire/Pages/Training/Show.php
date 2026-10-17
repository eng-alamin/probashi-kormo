<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Training;

use App\Models\TrainingCourse;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::site')]
class Show extends Component
{
    public TrainingCourse $course;

    public function mount(TrainingCourse $course): void
    {
        abort_unless($course->is_active, 404);

        $this->course = $course;
    }

    public function render(): View
    {
        return view('livewire.pages.training.show', [
            'others' => TrainingCourse::active()->ordered()->whereKeyNot($this->course->getKey())->get(['title', 'slug', 'icon']),
        ])->title($this->course->title.' Course')
            ->layoutData(['description' => $this->course->summary]);
    }
}
