@props(['title', 'text' => null])
@use('App\Support\SafeRoute')
<section class="bg-gradient-to-r from-pk-red to-red-800 py-16 text-center text-white">
  <div class="reveal mx-auto w-[92%] max-w-6xl">
    <h2 class="mb-2 text-3xl font-bold md:text-4xl">{{ $title }}</h2>
    @if ($text)
      <p class="mb-6 text-red-100">{{ $text }}</p>
    @endif
    <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line">Contact Us</a>
  </div>
</section>
