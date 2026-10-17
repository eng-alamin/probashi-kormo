<div>
  <x-site.banner title="Gallery" :crumbs="[['Gallery', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-6xl">
      <div class="mb-10 text-center">
        <span class="eyebrow">Our Moments</span>
        <h2 class="title">Photos From Our Work</h2>
        <p class="lead mx-auto">A look at our office, training classes and workers departing for their jobs abroad.</p>
      </div>

      @if ($items->isNotEmpty())
        <div class="mb-10 flex flex-wrap justify-center gap-3">
          <button data-filter="all" class="inline-block rounded-full px-6 py-2 text-sm font-semibold shadow transition bg-pk-red text-white">All</button>
          @foreach ($categories as $category)
            @if ($items->contains(fn ($i) => $i->category === $category))
              <button data-filter="{{ $category->value }}" class="inline-block rounded-full px-6 py-2 text-sm font-semibold shadow transition bg-white text-pk-dark">{{ $category->label() }}</button>
            @endif
          @endforeach
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($items as $item)
            <figure data-cat="{{ $item->category?->value }}" class="overflow-hidden rounded-2xl bg-white shadow-lg shadow-green-900/10 transition hover:-translate-y-1">
              <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?: $item->caption ?: 'Photo' }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
              @if ($item->title || $item->caption)
                <figcaption class="p-4 text-sm font-medium text-pk-dark">{{ $item->title ?: $item->caption }}</figcaption>
              @endif
            </figure>
          @endforeach
        </div>
      @else
        <p class="text-center text-slate-500">Photos will be added here soon.</p>
      @endif
    </div>
  </section>

  <x-site.cta title="Want to Visit Us?" text="Come to our office or send a message today." />
</div>
