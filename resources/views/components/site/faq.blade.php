@props(['title' => 'Frequently Asked Questions', 'items' => []])
<div class="reveal">
  @if ($title)
    <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $title }}</h2>
  @endif
  <div class="space-y-4">
    @foreach ($items as [$question, $answer])
      <details class="group rounded-xl bg-white p-5 shadow-lg shadow-green-900/10">
        <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-pk-dark">{{ $question }}<span class="text-pk-red transition group-open:rotate-45">+</span></summary>
        <p class="mt-3 text-sm text-slate-600">{{ $answer }}</p>
      </details>
    @endforeach
  </div>
</div>
