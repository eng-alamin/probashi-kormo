<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('About Us')]
class About extends Component
{
    public function render(): View
    {
        return view('livewire.pages.about');
    }
}
