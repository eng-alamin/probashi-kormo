<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit service' : 'New service' }}</h2><p>Details shown on the website service page</p></div>
    <a href="{{ route('admin.services.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.field label="Service title" model="title" :required="true" />
      <x-admin.field label="Icon" model="icon" placeholder="🛂" maxlength="100" hint="Paste one emoji." />

      <x-admin.field label="URL slug" model="slug" :hint="$isEdit ? 'Changing this changes the public link of the service.' : 'Leave blank to make it from the title.'" />
      <x-admin.field label="Display order" model="sort_order" type="number" hint="Smaller number shows first." />

      <x-admin.textarea label="Short summary" model="summary" :rows="2" hint="Shown on the services list and home page. Up to 500 characters." />
      <x-admin.textarea label="Description" model="description" :rows="6" />
      <x-admin.textarea label="What we do" model="what_we_do" :rows="5" hint="One item per line." />
      <x-admin.textarea label="What to bring" model="what_to_bring" :rows="5" hint="One item per line." />

      <x-admin.check label="Show on website" model="is_active" />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create service' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
