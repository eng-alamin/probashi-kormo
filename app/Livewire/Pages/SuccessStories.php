<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\SuccessStory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Success Stories')]
class SuccessStories extends Component
{
    public function render(): View
    {
        return view('livewire.pages.stories.index', [
            'stories' => SuccessStory::published()->ordered()->with('country:id,name')->get(),
        ]);
    }
}
