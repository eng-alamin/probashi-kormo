<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit post' : 'New post' }}</h2><p>News or notice shown on the website</p></div>
    <a href="{{ route('admin.news.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.field label="Title" model="title" :required="true" :span="true" />

      <x-admin.field label="URL slug" model="slug" :hint="$isEdit ? 'Changing this changes the public link of the post.' : 'Leave blank to make it from the title.'" :span="true" />

      <x-admin.textarea label="Short summary" model="excerpt" :rows="2" hint="Shown on the news list. Up to 500 characters. Leave blank to use the start of the text." />
      <x-admin.textarea label="Text" model="body" :rows="12" :required="true" hint="Leave a blank line between paragraphs. Plain text only." />

      <x-admin.image label="Cover image" model="cover" :preview="$cover" :current="\App\Support\ImageStore::url($currentCover)" removeModel="removeCover" hint="Optional. JPG, PNG or WebP, up to 4 MB." />

      <div class="form-section">Publishing</div>
      <x-admin.check label="Published" model="is_published" hint="Off = draft, only you can see it in the admin." />
      <x-admin.field label="Publish date and time" model="published_at" type="datetime-local" hint="Leave blank to publish right now. A future time schedules the post." />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save,cover">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Save post' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.news.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
