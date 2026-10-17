<div>
  <x-site.banner :title="$post->title" :crumbs="[['News', route('news')], [$post->title, null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-3xl">
      <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-pk-red">📅 {{ $post->published_at->format('d M Y') }}</p>
      @if ($post->cover_image_path)
        <img src="{{ asset('storage/'.$post->cover_image_path) }}" alt="{{ $post->title }}" class="mb-6 w-full rounded-2xl object-cover">
      @endif
      {{-- body is plain text for now (escaped); blank lines make paragraphs --}}
      <div class="space-y-4 text-slate-600">
        @foreach (preg_split('/\R{2,}/u', trim($post->body)) ?: [] as $paragraph)
          <p class="whitespace-pre-line">{{ $paragraph }}</p>
        @endforeach
      </div>
      <a href="{{ route('news') }}" class="mt-8 inline-block text-sm font-semibold text-pk-green hover:text-pk-red">← All news</a>
    </div>
  </section>
</div>
