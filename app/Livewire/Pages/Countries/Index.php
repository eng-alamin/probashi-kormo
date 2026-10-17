<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Countries;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Countries We Work With')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.pages.countries.index', [
            'countries' => Country::active()->ordered()->get(),
        ]);
    }
}
