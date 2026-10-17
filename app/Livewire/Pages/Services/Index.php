<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Services;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Our Services')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.pages.services.index', [
            'services' => Service::active()->ordered()->get(),
        ]);
    }
}
