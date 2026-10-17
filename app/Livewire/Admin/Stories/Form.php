<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Stories;

use App\Models\Country;
use App\Models\SuccessStory;
use App\Support\ImageStore;
use App\Support\Slug;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class Form extends Component
{
    use WithFileUploads;

    public ?int $storyId = null;

    public string $name = '';

    public string $trade = '';

    public string $country_id = '';

    public string $story = '';

    public string $sort_order = '0';

    public bool $is_published = true;

    public ?string $currentPhoto = null;

    public $photo = null;

    public bool $removePhoto = false;

    // The route parameter is named "record" (not "story") because "story" is a form field below.
    public function mount(?SuccessStory $record = null): void
    {
        if ($record === null || ! $record->exists) {
            $this->authorize('stories.create');

            return;
        }

        $this->authorize('stories.update');

        $this->storyId = $record->id;
        $this->name = $record->name;
        $this->trade = (string) $record->trade;
        $this->country_id = (string) $record->country_id;
        $this->story = $record->story;
        $this->sort_order = (string) ($record->sort_order ?? 0);
        $this->is_published = (bool) ($record->is_published ?? true);
        $this->currentPhoto = $record->photo_path;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'trade' => ['nullable', 'string', 'max:150'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'story' => ['required', 'string', 'min:10', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_published' => ['boolean'],
            'photo' => ['nullable', ...ImageStore::rules()],
            'removePhoto' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return ImageStore::messages('photo');
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'country_id' => 'country',
            'story' => 'story text',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->storyId === null ? 'stories.create' : 'stories.update');

        $this->name = trim($this->name);

        $this->validate();

        $payload = [
            'name' => $this->name,
            'trade' => Slug::blankToNull($this->trade),
            'country_id' => $this->country_id !== '' ? (int) $this->country_id : null,
            'story' => trim($this->story),
            'sort_order' => (int) $this->sort_order,
            'is_published' => $this->is_published,
        ];

        if ($this->storyId === null) {
            if ($this->photo !== null) {
                $payload['photo_path'] = ImageStore::put($this->photo, 'stories');
            }

            SuccessStory::create($payload);
            $message = 'Story created';
        } else {
            $story = SuccessStory::findOrFail($this->storyId);
            $oldPhoto = $story->photo_path;
            $replaced = false;

            if ($this->photo !== null) {
                $payload['photo_path'] = ImageStore::put($this->photo, 'stories');
                $replaced = true;
            } elseif ($this->removePhoto) {
                $payload['photo_path'] = null;
                $replaced = true;
            }

            $story->update($payload);

            if ($replaced) {
                ImageStore::delete($oldPhoto);
            }

            $message = 'Story updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.stories.index');
    }

    public function render(): View
    {
        return view('livewire.admin.stories.form', [
            'isEdit' => $this->storyId !== null,
            'countries' => Country::ordered()->get(['id', 'name']),
        ])->title($this->storyId === null ? 'New story' : 'Edit story');
    }
}
