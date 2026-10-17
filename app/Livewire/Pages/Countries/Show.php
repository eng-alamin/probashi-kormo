<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Countries;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::site')]
class Show extends Component
{
    public Country $country;

    public function mount(Country $country): void
    {
        abort_unless($country->is_active, 404);

        $this->country = $country;
    }

    public function render(): View
    {
        return view('livewire.pages.countries.show', [
            'others' => Country::active()->ordered()->whereKeyNot($this->country->getKey())->get(['name', 'slug', 'flag']),
        ])->title('Working in '.$this->country->name)
            ->layoutData(['description' => $this->country->summary]);
    }
}
