<div>
  <x-site.banner title="Countries We Work With" :crumbs="[['Countries', null]]" />

  <section class="py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">Where We Send Workers</span>
      <h2 class="title">Countries We Work With</h2>
      <p class="lead mx-auto">Job availability changes with demand. Contact us to know the latest openings for each country.</p>
      <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($countries as $country)
          <div class="card">
            <div class="mb-3 text-5xl">{{ $country->flag }}</div>
            <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $country->name }}</h3>
            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-pk-red">Common job sectors</p>
            <p class="text-[.95rem] text-slate-600">{{ $country->summary }}</p>
            <a href="{{ route('countries.show', $country) }}" class="mt-4 inline-block text-sm font-semibold text-pk-green hover:text-pk-red">View details →</a>
          </div>
        @empty
          <p class="col-span-full text-slate-500">Countries will be listed here soon.</p>
        @endforelse
      </div>
    </div>
  </section>

  <section class="bg-pk-bg py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">How It Works</span>
      <h2 class="title">Your Path to Working Abroad</h2>
      <div class="mt-11">
        <x-site.steps :steps="[
          ['Choose Country', 'Tell us your preferred destination and job.'],
          ['Check Demand', 'We share available jobs and requirements.'],
          ['Process Documents', 'Visa, medical and clearance with our help.'],
          ['Fly & Work', 'Travel with full pre-departure support.'],
        ]" />
      </div>
    </div>
  </section>

  <x-site.cta title="Not Sure Which Country?" text="Tell us your skills and we will suggest the best option." />
</div>
