<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Training;

use App\Models\TrainingCourse;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Skill Training')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.pages.training.index', [
            'courses' => TrainingCourse::active()->ordered()->get(),
        ]);
    }
}
