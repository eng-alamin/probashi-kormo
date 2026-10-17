<?php

declare(strict_types=1);

namespace App\Livewire\Admin\News;

use App\Models\NewsPost;
use App\Support\ImageStore;
use App\Support\Slug;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class Form extends Component
{
    use WithFileUploads;

    public ?int $postId = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public bool $is_published = false;

    /** datetime-local value, e.g. 2026-10-17T09:30 */
    public string $published_at = '';

    public ?string $currentCover = null;

    public $cover = null;

    public bool $removeCover = false;

    public function mount(?NewsPost $post = null): void
    {
        if ($post === null || ! $post->exists) {
            $this->authorize('news.create');

            return;
        }

        $this->authorize('news.update');

        $this->postId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = (string) $post->excerpt;
        $this->body = $post->body;
        $this->is_published = (bool) $post->is_published;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->currentCover = $post->cover_image_path;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('news_posts', 'slug')->ignore($this->postId)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:2', 'max:50000'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', ...ImageStore::rules()],
            'removeCover' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'slug.regex' => 'Use only small letters, numbers and hyphens, for example new-office-hours.',
            ...ImageStore::messages('cover'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'published_at' => 'publish date',
            'cover' => 'cover image',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->postId === null ? 'news.create' : 'news.update');

        $this->title = trim($this->title);
        $this->slug = strtolower(trim($this->slug));

        $this->validate();

        // A published post needs a date, otherwise it would stay hidden. Blank means "now".
        $publishedAt = Slug::blankToNull($this->published_at) !== null ? Carbon::parse($this->published_at) : null;

        if ($this->is_published && $publishedAt === null) {
            $publishedAt = now();
        }

        $payload = [
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : Slug::unique(NewsPost::class, $this->title, $this->postId, 'news'),
            'excerpt' => Slug::blankToNull($this->excerpt),
            'body' => trim($this->body),
            'is_published' => $this->is_published,
            'published_at' => $publishedAt,
        ];

        if ($this->postId === null) {
            if ($this->cover !== null) {
                $payload['cover_image_path'] = ImageStore::put($this->cover, 'news');
            }

            $payload['author_id'] = auth()->id();
            NewsPost::create($payload);
            $message = $this->is_published ? 'Post published' : 'Draft saved';
        } else {
            $post = NewsPost::findOrFail($this->postId);
            $oldCover = $post->cover_image_path;
            $replaced = false;

            if ($this->cover !== null) {
                $payload['cover_image_path'] = ImageStore::put($this->cover, 'news');
                $replaced = true;
            } elseif ($this->removeCover) {
                $payload['cover_image_path'] = null;
                $replaced = true;
            }

            $post->update($payload);

            if ($replaced) {
                ImageStore::delete($oldCover);
            }

            $message = 'Post updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.news.index');
    }

    public function render(): View
    {
        return view('livewire.admin.news.form', [
            'isEdit' => $this->postId !== null,
        ])->title($this->postId === null ? 'New post' : 'Edit post');
    }
}
