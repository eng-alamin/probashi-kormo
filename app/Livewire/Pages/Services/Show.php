<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Services;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::site')]
class Show extends Component
{
    public Service $service;

    public function mount(Service $service): void
    {
        abort_unless($service->is_active, 404);

        $this->service = $service;
    }

    public function render(): View
    {
        return view('livewire.pages.services.show', [
            'others' => Service::active()->ordered()->whereKeyNot($this->service->getKey())->get(['title', 'slug', 'icon']),
        ])->title($this->service->title)
            ->layoutData(['description' => $this->service->summary]);
    }
}
