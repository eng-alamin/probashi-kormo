<div>
  @if (session('error'))
    <div class="field-error show" role="alert" style="margin:0 0 14px;"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ session('error') }}</div>
  @endif

  <form wire:submit="login" novalidate>
    <div class="mb-3">
      <label class="form-label" for="email">Email</label>
      <input id="email" type="email" wire:model="form.email" class="form-control @error('form.email') is-invalid @enderror" autocomplete="username" autofocus required>
      @error('form.email')
        <div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label class="form-label" for="password">Password</label>
      <input id="password" type="password" wire:model="form.password" class="form-control @error('form.password') is-invalid @enderror" autocomplete="current-password" required>
      @error('form.password')
        <div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>
      @enderror
    </div>

    <div class="form-check mb-4">
      <input id="remember" type="checkbox" wire:model="form.remember" class="form-check-input">
      <label class="form-check-label" for="remember" style="color:var(--text-soft); font-size:.85rem;">Keep me signed in</label>
    </div>

    <button type="submit" class="btn btn-cobalt w-100" wire:loading.attr="disabled" wire:target="login">
      <span wire:loading.remove wire:target="login">Sign in</span>
      <span wire:loading wire:target="login">Signing in…</span>
    </button>
  </form>
</div>
