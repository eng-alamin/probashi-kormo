@use('App\Support\SafeRoute')
<div>
  <x-site.banner title="Success Stories" :crumbs="[['Success Stories', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-6xl">
      <div class="mb-10 text-center">
        <span class="eyebrow">Real People</span>
        <h2 class="title">Workers Who Built a Better Future</h2>
        <p class="lead mx-auto">Hear from people who trained with us and now work abroad.</p>
      </div>

      @if ($stories->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-3">
          @foreach ($stories as $story)
            <div class="card">
              @if ($story->photo_path)
                <img src="{{ asset('storage/'.$story->photo_path) }}" alt="{{ $story->name }}" loading="lazy" class="mb-3 h-16 w-16 rounded-full object-cover">
              @else
                <div class="mb-3 grid h-16 w-16 place-items-center rounded-full bg-pk-green/10 text-3xl">👤</div>
              @endif
              <h3 class="font-semibold text-pk-dark">{{ $story->name }}</h3>
              @if ($story->trade || $story->country)
                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-pk-red">{{ collect([$story->trade, $story->country?->name])->filter()->implode(' – ') }}</p>
              @endif
              <p class="whitespace-pre-line text-sm text-slate-600">{{ $story->story }}</p>
            </div>
          @endforeach
        </div>
      @else
        <div class="mx-auto max-w-xl rounded-2xl bg-pk-bg p-10 text-center">
          <div class="mb-3 text-5xl">🌟</div>
          <h3 class="mb-2 text-lg font-semibold text-pk-dark">Stories coming soon</h3>
          <p class="mb-5 text-sm text-slate-600">We share real experiences of our workers, with their permission.</p>
          <a href="{{ SafeRoute::url('apply') }}" class="btn btn-red">Apply Now</a>
        </div>
      @endif
    </div>
  </section>

  <x-site.cta title="Be the Next Success Story" text="Join us and start your journey abroad." />
</div>
