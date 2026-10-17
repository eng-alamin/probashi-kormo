<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit course' : 'New course' }}</h2><p>Details shown on the website course page</p></div>
    <a href="{{ route('admin.trainings.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="form-grid">
      <x-admin.field label="Course title" model="title" :required="true" />
      <x-admin.field label="Icon" model="icon" placeholder="⚡" maxlength="100" hint="Paste one emoji." />

      <x-admin.field label="URL slug" model="slug" :hint="$isEdit ? 'Changing this changes the public link of the course.' : 'Leave blank to make it from the title.'" />
      <x-admin.field label="Display order" model="sort_order" type="number" hint="Smaller number shows first." />

      <x-admin.textarea label="Short summary" model="summary" :rows="2" hint="Shown on the training list and home page. Up to 500 characters." />
      <x-admin.textarea label="Description" model="description" :rows="5" />

      <div class="form-section">Course details</div>
      <x-admin.field label="Duration (weeks)" model="duration_weeks" type="number" hint="Leave blank to show 'Contact us'." />
      <x-admin.field label="Course fee (৳)" model="fee" type="number" hint="Leave blank to show 'Contact us'." />
      <x-admin.field label="Class days" model="class_days" placeholder="e.g. Sat – Thu, 10am – 1pm" />
      <x-admin.field label="Certificate" model="certificate_info" placeholder="e.g. Certificate on completion" />

      <div class="form-section">Lists (one item per line)</div>
      <x-admin.textarea label="What you will learn" model="learn_points" :rows="5" />
      <x-admin.textarea label="Career options" model="career_points" :rows="4" />
      <x-admin.textarea label="Who can join" model="eligibility" :rows="4" />

      <x-admin.check label="Show on website" model="is_active" />
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create course' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.trainings.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
