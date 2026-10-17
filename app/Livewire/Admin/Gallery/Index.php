<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Gallery;

use App\Enums\GalleryCategory;
use App\Models\GalleryItem;
use App\Support\ImageStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Gallery')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    /** a GalleryCategory value, or "none" for photos without a category */
    #[Url(except: '')]
    public string $category = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('gallery.update');

        $item = GalleryItem::findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
        $this->dispatch('toast', message: $item->is_active ? 'Photo is now visible on the website' : 'Photo hidden from the website', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('gallery.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('gallery.delete');

        if ($this->deletingId !== null) {
            $item = GalleryItem::find($this->deletingId);

            if ($item !== null) {
                ImageStore::delete($item->image_path);
                $item->delete();
                $this->dispatch('toast', message: 'Photo deleted', type: 'ok');
            }
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('gallery.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $items = GalleryItem::query()
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('caption', 'like', $like)))
            ->when($this->category === 'none', fn ($q) => $q->whereNull('category'))
            ->when(GalleryCategory::tryFrom($this->category) !== null, fn ($q) => $q->where('category', $this->category))
            ->ordered()
            ->paginate(15);

        return view('livewire.admin.gallery.index', [
            'items' => $items,
            'categories' => GalleryCategory::cases(),
        ]);
    }
}
