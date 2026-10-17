<div>
  <x-site.banner :title="$service->title" :crumbs="[['Services', route('services')], [$service->title, null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl gap-10 lg:grid-cols-[1.7fr_1fr]">
      <div class="space-y-10">
        <div class="reveal">
          <div class="mb-4 text-6xl">{{ $service->icon }}</div>
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $service->title }}</h2>
          <p class="text-slate-600">{{ $service->description ?: $service->summary }}</p>
        </div>
        <x-site.checklist title="What We Do" :text="$service->what_we_do" />
        <x-site.checklist title="What You Need to Bring" :text="$service->what_to_bring" />
      </div>
      <aside class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-lg shadow-green-900/10">
          <h3 class="mb-2 font-semibold text-pk-dark">Quick Info</h3>
          <x-site.info-row label="Service" :value="$service->title" />
          <x-site.info-row label="Cost" value="Explained in advance" />
          <x-site.info-row label="Support" value="Start to finish" />
        </div>
        <x-site.help-box />
      </aside>
    </div>
  </section>

  <x-site.chips title="Other Services" route="services.show" :items="$others->map(fn ($s) => [$s->icon.' '.$s->title, $s->slug])->all()" />
  <x-site.cta title="Have a Question? Ask Us." text="We will explain clearly, with no hidden costs." />
</div>
