<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\GalleryCategory;
use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::site')]
#[Title('Gallery')]
class Gallery extends Component
{
    public function render(): View
    {
        return view('livewire.pages.gallery.index', [
            'items' => GalleryItem::active()->ordered()->get(),
            'categories' => GalleryCategory::cases(),
        ]);
    }
}
