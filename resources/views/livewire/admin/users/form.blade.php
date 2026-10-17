<div>
  <div class="page-head">
    <div><h2>{{ $isEdit ? 'Edit staff user' : 'New staff user' }}</h2><p>Sign-in details and role</p></div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel" autocomplete="off">
    <div class="form-grid">
      <x-admin.field label="Full name" model="name" :required="true" />
      <x-admin.field label="Email" model="email" type="email" :required="true" autocomplete="off" />

      <x-admin.field :label="$isEdit ? 'New password' : 'Password'" model="password" type="password" :required="! $isEdit" autocomplete="new-password" :hint="$isEdit ? 'Leave blank to keep the current password.' : 'At least 10 characters, with letters and numbers.'" />
      <x-admin.field label="Confirm password" model="password_confirmation" type="password" :required="! $isEdit" autocomplete="new-password" />

      <div class="form-section">Access</div>
      <x-admin.select label="Role" model="role" :required="true" :disabled="$isSelf" :hint="$isSelf ? 'You cannot change your own role.' : 'What this person can do is set on the Roles & Permissions page.'">
        @foreach ($roleOptions as $r)
          <option value="{{ $r }}">{{ $r }}</option>
        @endforeach
        @if ($role !== '' && ! in_array($role, $roleOptions, true))
          <option value="{{ $role }}">{{ $role }}</option>
        @endif
      </x-admin.select>
      @if ($isSelf)
        <div class="form-field"><div class="form-hint">Your own account always stays active.</div></div>
      @else
        <x-admin.check label="Account active" model="is_active" hint="Off = cannot sign in." />
      @endif
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Save changes' : 'Create account' }}</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
