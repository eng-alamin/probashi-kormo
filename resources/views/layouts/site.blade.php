@use('App\Support\SafeRoute')
@use('App\Support\Site')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
  $seoTitle = (isset($title) ? $title.' | ' : '').config('site.name');
  $seoDescription = ! empty($description) ? $description : config('site.name').' – '.config('site.tagline');
  $seoImage = ! empty($image) ? $image : asset('site/img/logo.jpg');
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ url()->current() }}">
@if (! empty($noindex))
<meta name="robots" content="noindex, follow">
@endif
<meta property="og:site_name" content="{{ config('site.name') }}">
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta name="twitter:card" content="{{ ! empty($image) ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
@isset($jsonLd)
<script type="application/ld+json">{!! \App\Support\Seo::json($jsonLd) !!}</script>
@endisset
<link rel="icon" href="{{ asset('site/img/logo.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('site/css/tailwind.css') }}">
<link rel="stylesheet" href="{{ asset('site/css/extra.css') }}">
<link rel="stylesheet" href="{{ asset('site/css/glass.css') }}">
@livewireStyles
</head>
<body class="bg-white font-sans leading-relaxed text-slate-800">

<!-- Topbar (shows only the details that are filled in) -->
@if (Site::phone() || Site::email())
<div class="bg-pk-dark py-2 text-xs text-green-100">
  <div class="mx-auto flex w-[92%] max-w-6xl flex-wrap justify-center gap-x-6 gap-y-1 sm:justify-between">
    @if (Site::phone())<a href="{{ Site::phoneHref() }}" class="hover:text-white">📞 {{ Site::phone() }}</a>@endif
    @if (Site::email())<a href="mailto:{{ Site::email() }}" class="hover:text-white">✉ {{ Site::email() }}</a>@endif
  </div>
</div>
@endif

<!-- Header -->
<header class="sticky top-0 z-50 bg-white shadow">
  <div class="relative mx-auto flex h-[74px] w-[92%] max-w-6xl items-center justify-between">
    <a href="{{ SafeRoute::url('home') }}" class="flex items-center gap-2.5 font-bold leading-tight text-pk-dark">
      <img src="{{ asset('site/img/logo.jpg') }}" alt="{{ config('site.short_name') }} logo" class="h-12 w-12 rounded-full object-cover">
      <span>{{ config('site.short_name') }}<small class="block text-[.72rem] font-medium text-pk-red">Overseas &amp; Travels</small></span>
    </a>
    <button id="burger" type="button" aria-label="Menu" aria-expanded="false" class="text-3xl text-pk-dark lg:hidden">☰</button>
    <nav id="menu" class="absolute left-0 right-0 top-[74px] hidden bg-white shadow-xl lg:static lg:block lg:shadow-none">
      <ul class="flex flex-col gap-0 px-2 py-3 lg:flex-row lg:items-center lg:gap-6 lg:p-0">
        @foreach (config('site.nav') as $item)
          @if (! empty($item['cta']))
            <li class="py-2.5 lg:py-0"><a href="{{ SafeRoute::url($item['route']) }}" class="btn btn-red !px-5 !py-2">{{ $item['label'] }}</a></li>
          @else
            <li class="border-b border-slate-100 py-2.5 lg:border-0 lg:py-0"><a href="{{ SafeRoute::url($item['route']) }}" class="nav-link {{ request()->routeIs($item['route']) ? 'border-pk-red text-pk-red' : '' }}">{{ $item['label'] }}</a></li>
          @endif
        @endforeach
      </ul>
    </nav>
  </div>
</header>

{{ $slot }}

<!-- Footer -->
<footer class="bg-[#0b2a21] pt-14 text-[.92rem] text-emerald-100/70">
  <div class="mx-auto grid w-[92%] max-w-6xl gap-9 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]">
    <div><h4 class="mb-3.5 font-semibold text-white">{{ config('site.name') }}</h4><p>{{ config('site.tagline') }}</p></div>
    <div>
      <h4 class="mb-3.5 font-semibold text-white">Quick Links</h4>
      <ul class="space-y-2">
        @foreach (config('site.footer.quick') as $item)
          <li><a class="hover:text-white" href="{{ SafeRoute::url($item['route']) }}">{{ $item['label'] }}</a></li>
        @endforeach
      </ul>
    </div>
    <div>
      <h4 class="mb-3.5 font-semibold text-white">More</h4>
      <ul class="space-y-2">
        @foreach (config('site.footer.more') as $item)
          <li><a class="hover:text-white" href="{{ SafeRoute::url($item['route']) }}">{{ $item['label'] }}</a></li>
        @endforeach
        <li><a class="hover:text-white" href="{{ config('site.facebook') }}" target="_blank" rel="noopener">Facebook</a></li>
      </ul>
    </div>
    <div>
      <h4 class="mb-3.5 font-semibold text-white">Contact</h4>
      <ul class="space-y-2">
        @if (Site::address())<li>📍 {{ Site::address() }}</li>@endif
        @if (Site::phone())<li>📞 <a class="hover:text-white" href="{{ Site::phoneHref() }}">{{ Site::phone() }}</a></li>@endif
        @if (Site::email())<li>✉ <a class="hover:text-white" href="mailto:{{ Site::email() }}">{{ Site::email() }}</a></li>@endif
        <li><a class="hover:text-white" href="{{ SafeRoute::url('contact') }}">Send us a message →</a></li>
      </ul>
    </div>
  </div>
  <div class="mt-10 border-t border-emerald-900 py-4 text-center text-sm">
    © <span data-year>{{ now()->year }}</span> {{ config('site.name') }}. All rights reserved.
    @foreach (config('site.footer.legal') as $item)
      &nbsp;|&nbsp; <a class="hover:text-white" href="{{ SafeRoute::url($item['route']) }}">{{ $item['label'] }}</a>
    @endforeach
  </div>
</footer>

@if (Site::whatsappUrl())
<a href="{{ Site::whatsappUrl() }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="fixed bottom-5 right-5 z-50 grid h-14 w-14 place-items-center rounded-full bg-[#25d366] text-2xl text-white shadow-xl">💬</a>
@endif

@livewireScripts
<script src="{{ asset('site/js/main.js') }}"></script>
</body>
</html>
