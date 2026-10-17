@props(['title' => null, 'steps' => []])
<div class="reveal">
  @if ($title)
    <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $title }}</h2>
  @endif
  <div class="grid gap-6 text-center sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($steps as $i => [$name, $text])
      <div class="p-2">
        <div class="mx-auto mb-3.5 grid h-14 w-14 place-items-center rounded-full bg-pk-green text-xl font-bold text-white">{{ $i + 1 }}</div>
        <h3 class="font-semibold text-pk-dark">{{ $name }}</h3>
        <p class="text-sm text-slate-600">{{ $text }}</p>
      </div>
    @endforeach
  </div>
</div>
