<?php

declare(strict_types=1);

namespace App\Livewire\Admin\News;

use App\Models\NewsPost;
use App\Support\ImageStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('News & Notice')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    /** live | scheduled | draft */
    #[Url(except: '')]
    public string $state = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedState(): void
    {
        $this->resetPage();
    }

    public function togglePublished(int $id): void
    {
        $this->authorize('news.update');

        $post = NewsPost::findOrFail($id);

        if ($post->is_published) {
            $post->update(['is_published' => false]);
            $message = 'Post moved back to draft';
        } else {
            $post->update([
                'is_published' => true,
                'published_at' => $post->published_at ?? now(),
            ]);
            $message = $post->published_at->isFuture() ? 'Post scheduled' : 'Post is now live on the website';
        }

        $this->dispatch('toast', message: $message, type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('news.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('news.delete');

        if ($this->deletingId !== null) {
            $post = NewsPost::find($this->deletingId);

            if ($post !== null) {
                ImageStore::delete($post->cover_image_path);
                $post->delete();
                $this->dispatch('toast', message: 'Post deleted', type: 'ok');
            }
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('news.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $posts = NewsPost::query()
            ->when($term !== '', fn ($q) => $q->where('title', 'like', $like))
            ->when($this->state === 'live', fn ($q) => $q->published())
            ->when($this->state === 'scheduled', fn ($q) => $q->where('is_published', true)->where('published_at', '>', now()))
            ->when($this->state === 'draft', fn ($q) => $q->where(fn ($w) => $w->where('is_published', false)->orWhereNull('published_at')))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.admin.news.index', ['posts' => $posts]);
    }
}
