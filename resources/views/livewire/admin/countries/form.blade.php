<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit country' : 'New country' }}</h2><p>Details shown on the website country page</p></div>
    <a href="{{ route('admin.countries.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.field label="Country name" model="name" :required="true" />
      <x-admin.field label="Flag" model="flag" placeholder="🇸🇦" maxlength="16" hint="Paste a flag emoji." />

      <x-admin.field label="URL slug" model="slug" :hint="$isEdit ? 'Changing this changes the public link of the country.' : 'Leave blank to make it from the name.'" />
      <x-admin.field label="Display order" model="sort_order" type="number" hint="Smaller number shows first." />

      <x-admin.textarea label="Short summary" model="summary" :rows="2" hint="Shown on the countries list. Up to 500 characters." />
      <x-admin.textarea label="Description" model="description" :rows="6" />
      <x-admin.textarea label="Job sectors" model="job_sectors" :rows="5" hint="One sector per line." />
      <x-admin.field label="Contract information" model="contract_info" :span="true" placeholder="e.g. 2 years, renewable" />
      <x-admin.textarea label="Extra information" model="extra_info" :rows="4" />

      <x-admin.check label="Show on website" model="is_active" />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create country' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.countries.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
