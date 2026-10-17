<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit job' : 'New job' }}</h2><p>Details shown on the website job page</p></div>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.select label="Country" model="country_id" :required="true">
        <option value="">Choose a country</option>
        @foreach ($countries as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </x-admin.select>
      <x-admin.field label="Category" model="category" placeholder="e.g. Construction" />

      <x-admin.field label="Job title" model="title" :required="true" :span="true" />
      <x-admin.field label="URL slug" model="slug" :span="true" :hint="$isEdit ? 'Changing this changes the public link of the job.' : 'Leave blank to make it from the title.'" />
      <x-admin.textarea label="Short summary" model="summary" :rows="2" hint="Shown in lists and search results. Up to 500 characters." />

      <div class="form-section">Details</div>
      <x-admin.textarea label="Description" model="description" :rows="6" />
      <x-admin.textarea label="Requirements" model="requirements" :rows="5" hint="One item per line." />
      <x-admin.textarea label="Benefits" model="benefits" :rows="4" hint="One item per line." />

      <div class="form-section">Salary and dates</div>
      <x-admin.field label="Minimum salary (per month)" model="salary_min" type="number" />
      <x-admin.field label="Maximum salary (per month)" model="salary_max" type="number" />
      <x-admin.field label="Currency" model="salary_currency" placeholder="SAR" maxlength="3" hint="3-letter code. Leave all salary fields blank to show 'As per contract'." />
      <x-admin.field label="Vacancies" model="vacancies" type="number" />
      <x-admin.field label="Last date to apply" model="deadline" type="date" hint="Leave blank for no deadline." />

      <div class="form-section">Visibility</div>
      <x-admin.check label="Show on website" model="is_active" />
      <x-admin.check label="Featured" model="is_featured" hint="Marks the job with a star in this list. The public website does not use it yet." />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create job' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.jobs.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
