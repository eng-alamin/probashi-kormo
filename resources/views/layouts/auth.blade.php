<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="robots" content="noindex, nofollow">
<title>{{ isset($title) ? $title.' — ' : '' }}{{ config('site.short_name') }}</title>
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
<style>
  .auth-wrap{position:relative; z-index:1; min-height:100vh; min-height:100dvh; display:grid; place-items:center; padding:24px;}
  .auth-card{width:100%; max-width:420px; padding:32px 28px;}
  .auth-brand{text-align:center; margin-bottom:24px;}
  .auth-brand img{width:72px; height:72px; border-radius:50%; object-fit:cover; margin-bottom:12px;}
  .auth-brand h1{font-size:1.4rem; margin:0;}
  .auth-brand p{color:var(--text-soft); font-size:.85rem; margin:4px 0 0;}
</style>
@livewireStyles
</head>
<body>
<div class="bg-orbs" aria-hidden="true"><div class="orb orb1"></div><div class="orb orb2"></div><div class="orb orb3"></div></div>

<main class="auth-wrap">
  <div class="auth-card glass">
    <div class="auth-brand">
      <img src="{{ asset('site/img/logo.jpg') }}" alt="{{ config('site.short_name') }} logo">
      <h1>{{ config('site.short_name') }}</h1>
      <p>Admin Panel</p>
    </div>

    {{ $slot }}
  </div>
</main>

<div class="toast-wrap" id="toastWrap" aria-live="polite"></div>

@livewireScripts
</body>
</html>
