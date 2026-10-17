<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit story' : 'New story' }}</h2><p>Shown on the website success stories page</p></div>
    <a href="{{ route('admin.stories.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.field label="Name" model="name" :required="true" />
      <x-admin.field label="Trade / job" model="trade" placeholder="e.g. Electrician" />

      <x-admin.select label="Country" model="country_id">
        <option value="">Not specified</option>
        @foreach ($countries as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </x-admin.select>
      <x-admin.field label="Display order" model="sort_order" type="number" hint="Smaller number shows first." />

      <x-admin.textarea label="Story" model="story" :rows="6" :required="true" hint="Use the worker's own words, with their permission." />

      <x-admin.image label="Photo" model="photo" shape="round" :preview="$photo" :current="\App\Support\ImageStore::url($currentPhoto)" removeModel="removePhoto" hint="Optional. Shown as a round picture. JPG, PNG or WebP, up to 4 MB." />

      <x-admin.check label="Show on website" model="is_published" />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save,photo">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create story' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.stories.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
