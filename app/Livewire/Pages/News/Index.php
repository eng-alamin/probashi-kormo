<?php

declare(strict_types=1);

namespace App\Livewire\Pages\News;

use App\Models\NewsPost;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('News & Notices')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.pages.news.index', [
            // latest 30 for now; pagination can come with the admin module
            'posts' => NewsPost::published()->latest('published_at')->limit(30)->get(),
        ]);
    }
}
