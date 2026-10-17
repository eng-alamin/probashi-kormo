<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">
<title>{{ isset($title) ? $title.' — ' : '' }}{{ config('site.short_name') }} Admin</title>
<link rel="icon" href="{{ asset('site/img/logo.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
  (function () {
    try {
      document.documentElement.setAttribute('data-theme', localStorage.getItem('pk-admin-theme-v2') || 'light');
    } catch (e) {
      document.documentElement.setAttribute('data-theme', 'light');
    }
  })();
</script>
@vite(['resources/css/admin.css', 'resources/js/admin.js'])
@livewireStyles
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="bg-orbs" aria-hidden="true"><div class="orb orb1"></div><div class="orb orb2"></div><div class="orb orb3"></div></div>
<div class="sidebar-scrim" id="sidebarScrim"></div>

<div class="app-shell">
  @include('layouts.partials.admin-sidebar')

  <div class="main">
    @include('layouts.partials.admin-topbar')

    <main class="content" id="main-content">
      {{ $slot }}
    </main>

    @include('layouts.partials.admin-tabbar')
  </div>
</div>

<div class="toast-wrap" id="toastWrap" aria-live="polite"></div>
@if (session('toast'))
  <div data-flash-toast="{{ session('toast') }}" hidden></div>
@endif

@livewireScripts
</body>
</html>
