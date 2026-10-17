@props(['title', 'items' => [], 'route'])
@use('App\Support\SafeRoute')
@if (count($items))
  <section class="bg-pk-bg py-12">
    <div class="mx-auto w-[92%] max-w-6xl text-center">
      <h2 class="mb-6 text-xl font-bold text-pk-dark">{{ $title }}</h2>
      <div class="flex flex-wrap justify-center gap-3">
        @foreach ($items as [$label, $slug])
          <a href="{{ SafeRoute::exists($route) ? route($route, $slug) : '#' }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-pk-dark shadow-lg shadow-green-900/10 transition hover:text-pk-red">{{ $label }}</a>
        @endforeach
      </div>
    </div>
  </section>
@endif
