<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Stories;

use App\Models\SuccessStory;
use App\Support\ImageStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Success Stories')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function togglePublished(int $id): void
    {
        $this->authorize('stories.update');

        $story = SuccessStory::findOrFail($id);
        $story->update(['is_published' => ! $story->is_published]);
        $this->dispatch('toast', message: $story->is_published ? 'Story is now visible on the website' : 'Story hidden from the website', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('stories.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('stories.delete');

        if ($this->deletingId !== null) {
            $story = SuccessStory::find($this->deletingId);

            if ($story !== null) {
                ImageStore::delete($story->photo_path);
                $story->delete();
                $this->dispatch('toast', message: 'Story deleted', type: 'ok');
            }
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('stories.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $stories = SuccessStory::query()
            ->with('country:id,name')
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('trade', 'like', $like)))
            ->ordered()
            ->paginate(15);

        return view('livewire.admin.stories.index', ['stories' => $stories]);
    }
}
