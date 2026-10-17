<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Privacy Policy')]
class PrivacyPolicy extends Component
{
    public function render(): View
    {
        return view('livewire.pages.privacy-policy');
    }
}
