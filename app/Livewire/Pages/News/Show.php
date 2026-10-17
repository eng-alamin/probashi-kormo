<?php

declare(strict_types=1);

namespace App\Livewire\Pages\News;

use App\Models\NewsPost;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::site')]
class Show extends Component
{
    public NewsPost $post;

    public function mount(NewsPost $post): void
    {
        // unpublished or future-dated posts are not public
        abort_unless(
            $post->is_published && $post->published_at !== null && $post->published_at->lte(now()),
            404,
        );

        $this->post = $post;
    }

    public function render(): View
    {
        return view('livewire.pages.news.show')
            ->title($this->post->title)
            ->layoutData([
                'description' => $this->post->excerpt,
                'ogType' => 'article',
                'image' => $this->post->cover_image_path ? asset('storage/'.$this->post->cover_image_path) : null,
            ]);
    }
}
