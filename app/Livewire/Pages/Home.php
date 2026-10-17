<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Country;
use App\Models\Service;
use App\Models\TrainingCourse;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Manpower Recruiting Agency')]
class Home extends Component
{
    public function render(): View
    {
        return view('livewire.pages.home', [
            'services' => Service::active()->ordered()->get(),
            'countries' => Country::active()->ordered()->get(),
            'courses' => TrainingCourse::active()->ordered()->limit(8)->get(),
        ])->layoutData(['jsonLd' => Seo::organization()]);
    }
}
