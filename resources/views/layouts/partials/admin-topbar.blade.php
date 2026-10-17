@php
    $adminName = auth()->user()?->name ?? 'Admin';
    $adminEmail = auth()->user()?->email ?? config('site.email');
    $adminInitials = mb_strtoupper(collect(explode(' ', trim($adminName)))
        ->filter()
        ->map(fn (string $word): string => mb_substr($word, 0, 1))
        ->take(2)
        ->implode(''));
@endphp
<header class="topbar">
  <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
    <button class="icon-btn hamburger-btn" id="hamburgerBtn" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="sidebar"><i class="bi bi-list" aria-hidden="true"></i></button>
  </div>

  <div class="topbar-actions">
    <button class="icon-btn" id="themeToggleBtn" type="button" aria-label="Toggle dark or light theme"><i class="bi bi-moon-stars-fill" id="themeIcon" aria-hidden="true"></i></button>

    <div class="dropdown-wrap">
      <button class="icon-btn" id="notifBtn" type="button" aria-label="Notifications"><i class="bi bi-bell-fill" aria-hidden="true"></i></button>
      <div class="dropdown-panel" id="notifPanel" role="menu">
        <div class="dd-header">Notifications</div>
        <div class="dd-item" role="menuitem" style="cursor:default;"><i class="bi bi-check2-circle" aria-hidden="true"></i><span><span class="t" style="display:block;">No new notifications</span><span class="s">New applications and messages will appear here.</span></span></div>
      </div>
    </div>

    <div class="dropdown-wrap">
      <button class="admin-chip" id="avatarBtn" type="button" aria-label="Account menu">
        <span class="avatar" aria-hidden="true">{{ $adminInitials }}</span>
        <span style="line-height:1.1; text-align:left;">
          <span style="font-size:.82rem; font-weight:600; display:block;">{{ $adminName }}</span>
          <span style="font-size:.7rem; color:var(--text-soft); display:block;">{{ $adminEmail }}</span>
        </span>
        <i class="bi bi-chevron-down" style="font-size:.7rem; color:var(--text-soft);" aria-hidden="true"></i>
      </button>
      <div class="dropdown-panel wide" id="avatarPanel" role="menu">
        <a class="dd-item" href="{{ route('home') }}" target="_blank" rel="noopener" role="menuitem"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span class="t">View website</span></a>
        @if (Route::has('logout'))
          <div class="dd-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dd-item" type="submit" role="menuitem" style="width:100%; background:none; border:0; color:inherit; text-align:left;"><i class="bi bi-box-arrow-right" aria-hidden="true"></i><span class="t">Log out</span></button>
          </form>
        @endif
      </div>
    </div>
  </div>
</header>
