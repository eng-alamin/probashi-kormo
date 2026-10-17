<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Jobs;

use App\Models\JobPost;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Jobs Abroad')]
class Index extends Component
{
    public function render(): View
    {
        $jobs = JobPost::open()
            ->with('country:id,name,slug,flag')
            ->latest()
            ->get();

        return view('livewire.pages.jobs.index', [
            'jobs' => $jobs,
            // only countries that actually have an open job become filter buttons
            'countries' => $jobs->pluck('country')->filter()->unique('id')->sortBy('name')->values(),
        ]);
    }
}
