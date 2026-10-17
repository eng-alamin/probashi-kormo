@props(['title', 'text' => null])
@php($lines = \App\Support\Lines::split($text))
@if ($lines)
  <div class="reveal">
    <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $title }}</h2>
    <ul class="space-y-2.5">
      @foreach ($lines as $line)
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">{{ $line }}</li>
      @endforeach
    </ul>
    {{ $slot }}
  </div>
@endif
