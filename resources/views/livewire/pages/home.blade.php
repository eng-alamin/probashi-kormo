@use('App\Support\SafeRoute')
@use('App\Support\Site')
@php
  $home = Site::home();
@endphp
<div>
  <!-- Hero -->
  <section class="relative overflow-hidden bg-gradient-to-br from-pk-dark to-pk-green pb-28 pt-16 text-white md:pb-32 md:pt-24">
    <div class="pointer-events-none absolute -bottom-28 -right-28 h-96 w-96 rounded-full bg-pk-red/35"></div>
    <div class="relative mx-auto grid w-[92%] max-w-6xl items-center gap-10 md:grid-cols-[1.2fr_.8fr]">
      <div class="order-2 text-center md:order-1 md:text-left">
        <h1 class="mb-4 text-4xl font-bold leading-tight md:text-5xl">{{ $home['title'] }}@if (filled($home['highlight'])) <span class="text-red-200">{{ $home['highlight'] }}</span>@endif</h1>
        <p class="mx-auto mb-8 max-w-xl text-green-50 md:mx-0">{{ $home['text'] }}</p>
        <div class="flex flex-wrap justify-center gap-3.5 md:justify-start">
          <a href="{{ SafeRoute::url('apply') }}" class="btn btn-red">Apply Now</a>
          <a href="{{ SafeRoute::url('services') }}" class="btn btn-line">Our Services</a>
        </div>
      </div>
      <div class="order-1 justify-self-center md:order-2">
        <img src="{{ asset('site/img/logo.jpg') }}" alt="{{ config('site.short_name') }}" class="w-64 animate-bounce rounded-full shadow-2xl [animation-duration:4s] md:w-80">
      </div>
    </div>
  </section>

  <!-- Stats -->
  <section class="relative z-10 -mt-14">
    <div class="mx-auto grid w-[92%] max-w-6xl grid-cols-2 overflow-hidden rounded-2xl bg-white shadow-xl shadow-green-900/10 md:grid-cols-4">
      @php
        $statClasses = [
          'border-b border-r border-slate-100 p-7 text-center md:border-b-0',
          'border-b border-slate-100 p-7 text-center md:border-b-0 md:border-r',
          'border-r border-slate-100 p-7 text-center',
          'p-7 text-center',
        ];
      @endphp
      @foreach ($home['stats'] as $i => [$value, $label])
        <div class="{{ $statClasses[$i] }}"><b class="block text-3xl text-pk-red">{{ $value }}</b><span class="text-sm text-slate-500">{{ $label }}</span></div>
      @endforeach
    </div>
  </section>

  <!-- Services -->
  <section class="py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">What We Do</span>
      <h2 class="title">Complete Overseas Employment Solutions</h2>
      <p class="lead mx-auto">From skill development to departure, we handle every step so you can focus on building your career.</p>
      @if ($services->isNotEmpty())
        <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($services as $service)
            <a href="{{ route('services.show', $service) }}" class="card block">
              <div class="mb-3 text-4xl">{{ $service->icon }}</div>
              <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $service->title }}</h3>
              <p class="text-[.95rem] text-slate-600">{{ $service->summary }}</p>
              <span class="mt-3 inline-block text-sm font-semibold text-pk-red">Learn more →</span>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <!-- Countries -->
  @if ($countries->isNotEmpty())
    <section class="bg-pk-bg py-16 md:py-20">
      <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
        <span class="eyebrow">Where We Send Workers</span>
        <h2 class="title">Countries We Work With</h2>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
          @foreach ($countries as $country)
            <a href="{{ route('countries.show', $country) }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-pk-dark shadow-lg shadow-green-900/10 transition hover:text-pk-red">{{ $country->flag }} {{ $country->name }}</a>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <!-- Training -->
  @if ($courses->isNotEmpty())
    <section class="py-16 md:py-20">
      <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
        <span class="eyebrow">Train With Us</span>
        <h2 class="title">Skills That Get You Hired Abroad</h2>
        <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          @foreach ($courses as $course)
            <a href="{{ route('training.show', $course) }}" class="card block">
              <div class="mb-3 text-4xl">{{ $course->icon }}</div>
              <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $course->title }}</h3>
              <p class="text-[.95rem] text-slate-600">{{ $course->summary }}</p>
            </a>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <!-- CTA -->
  <section class="bg-gradient-to-r from-pk-red to-red-800 py-16 text-center text-white">
    <div class="reveal mx-auto w-[92%] max-w-6xl">
      <h2 class="mb-2 text-3xl font-bold md:text-4xl">Start Your Overseas Career Today</h2>
      <p class="mb-6 text-red-100">Talk to our team and take the first step.</p>
      <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line">Contact Us</a>
    </div>
  </section>
</div>
