<div>
  <x-site.banner title="News & Notices" :crumbs="[['News', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-6xl">
      <div class="mb-10 text-center">
        <span class="eyebrow">Stay Updated</span>
        <h2 class="title">Latest News &amp; Notices</h2>
      </div>

      @if ($posts->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-3">
          @foreach ($posts as $post)
            <a href="{{ route('news.show', $post) }}" class="card block">
              @if ($post->cover_image_path)
                <img src="{{ asset('storage/'.$post->cover_image_path) }}" alt="" loading="lazy" class="mb-3 aspect-[16/9] w-full rounded-xl object-cover">
              @endif
              <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-pk-red">📅 {{ $post->published_at->format('d M Y') }}</p>
              <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $post->title }}</h3>
              <p class="text-sm text-slate-600">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 140) }}</p>
              <span class="mt-3 inline-block text-sm font-semibold text-pk-red">Read more →</span>
            </a>
          @endforeach
        </div>
      @else
        <p class="text-center text-slate-500">No news or notices at the moment.</p>
      @endif
    </div>
  </section>

  <x-site.cta title="Have a Question?" text="Contact our team for the latest information." />
</div>
