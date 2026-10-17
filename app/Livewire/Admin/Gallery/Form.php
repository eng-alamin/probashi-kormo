<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Gallery;

use App\Enums\GalleryCategory;
use App\Models\GalleryItem;
use App\Support\ImageStore;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class Form extends Component
{
    use WithFileUploads;

    public ?int $itemId = null;

    public string $title = '';

    public string $caption = '';

    public string $category = '';

    public string $sort_order = '0';

    public bool $is_active = true;

    /** Path of the saved image (edit only). */
    public ?string $currentImage = null;

    /**
     * New photos (create: one or many).
     *
     * @var array<int, mixed>
     */
    public array $photos = [];

    /** Replacement photo (edit). */
    public $photo = null;

    public function mount(?GalleryItem $item = null): void
    {
        if ($item === null || ! $item->exists) {
            $this->authorize('gallery.create');

            return;
        }

        $this->authorize('gallery.update');

        $this->itemId = $item->id;
        $this->title = (string) $item->title;
        $this->caption = (string) $item->caption;
        $this->category = $item->category?->value ?? '';
        $this->sort_order = (string) ($item->sort_order ?? 0);
        $this->is_active = (bool) ($item->is_active ?? true);
        $this->currentImage = $item->image_path;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:200'],
            'caption' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', Rule::enum(GalleryCategory::class)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['boolean'],
        ];

        if ($this->itemId === null) {
            $rules['photos'] = ['required', 'array', 'min:1', 'max:20'];
            $rules['photos.*'] = ImageStore::rules();
        } else {
            $rules['photo'] = ['nullable', ...ImageStore::rules()];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'photos.required' => 'Choose at least one photo.',
            'photos.max' => 'You can add up to 20 photos at a time.',
            ...ImageStore::messages('photos.*'),
            ...ImageStore::messages('photo'),
        ];
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function save(): void
    {
        $this->authorize($this->itemId === null ? 'gallery.create' : 'gallery.update');

        $this->validate();

        $common = [
            'title' => Slug::blankToNull($this->title),
            'caption' => Slug::blankToNull($this->caption),
            'category' => $this->category !== '' ? $this->category : null,
            'sort_order' => (int) $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->itemId === null) {
            foreach ($this->photos as $file) {
                GalleryItem::create($common + ['image_path' => ImageStore::put($file, 'gallery')]);
            }

            $count = count($this->photos);
            $message = $count === 1 ? 'Photo added' : "{$count} photos added";
        } else {
            $item = GalleryItem::findOrFail($this->itemId);
            $oldPath = $item->image_path;
            $payload = $common;

            if ($this->photo !== null) {
                $payload['image_path'] = ImageStore::put($this->photo, 'gallery');
            }

            $item->update($payload);

            if ($this->photo !== null) {
                ImageStore::delete($oldPath);
            }

            $message = 'Photo updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.gallery.index');
    }

    public function render(): View
    {
        return view('livewire.admin.gallery.form', [
            'isEdit' => $this->itemId !== null,
            'categories' => GalleryCategory::cases(),
        ])->title($this->itemId === null ? 'Add photos' : 'Edit photo');
    }
}
