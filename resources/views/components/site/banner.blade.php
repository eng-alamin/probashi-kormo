@props(['title', 'crumbs' => []])
@use('App\Support\SafeRoute')
<section class="relative overflow-hidden bg-gradient-to-br from-pk-dark to-pk-green py-16 text-center text-white md:py-20">
  <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-pk-red/35"></div>
  <div class="relative mx-auto w-[92%] max-w-6xl">
    <h1 class="mb-2 text-3xl font-bold md:text-5xl">{{ $title }}</h1>
    <p class="text-green-100">
      <a href="{{ SafeRoute::url('home') }}" class="hover:underline">Home</a>
      @foreach ($crumbs as [$label, $url])
        &nbsp;/&nbsp;
        @if ($url)
          <a href="{{ $url }}" class="hover:underline">{{ $label }}</a>
        @else
          {{ $label }}
        @endif
      @endforeach
    </p>
  </div>
</section>
