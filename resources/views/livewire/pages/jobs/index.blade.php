@use('App\Support\SafeRoute')
<div>
  <x-site.banner title="Jobs Abroad" :crumbs="[['Jobs', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-6xl">
      <div class="mb-10 text-center">
        <span class="eyebrow">Current Openings</span>
        <h2 class="title">Find Your Job Abroad</h2>
        <p class="lead mx-auto">Choose a country to see available jobs, then apply online.</p>
      </div>

      @if ($jobs->isNotEmpty())
        <div class="mb-10 flex flex-wrap justify-center gap-3">
          <button data-filter="all" class="inline-block rounded-full px-6 py-2 text-sm font-semibold shadow transition bg-pk-red text-white">All</button>
          @foreach ($countries as $country)
            <button data-filter="{{ $country->slug }}" class="inline-block rounded-full px-6 py-2 text-sm font-semibold shadow transition bg-white text-pk-dark">{{ $country->name }}</button>
          @endforeach
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($jobs as $job)
            <a href="{{ route('jobs.show', $job) }}" data-cat="{{ $job->country?->slug }}" class="card block">
              @if ($job->category)
                <span class="mb-2 inline-block rounded-full bg-pk-red/10 px-3 py-1 text-xs font-semibold text-pk-red">{{ $job->category }}</span>
              @endif
              <h3 class="mb-1 text-lg font-semibold text-pk-dark">{{ $job->title }}</h3>
              @if ($job->country)
                <p class="text-sm text-slate-600">📍 {{ $job->country->name }}</p>
              @endif
              @if ($job->vacancies)
                <p class="text-sm text-slate-600">👥 Vacancies: {{ $job->vacancies }}</p>
              @endif
              @if ($job->deadline)
                <p class="text-sm text-slate-600">⏳ Last date: {{ $job->deadline->format('d M Y') }}</p>
              @endif
              <span class="mt-3 inline-block text-sm font-semibold text-pk-red">View details →</span>
            </a>
          @endforeach
        </div>
      @else
        <div class="mx-auto max-w-xl rounded-2xl bg-pk-bg p-10 text-center">
          <div class="mb-3 text-5xl">🧳</div>
          <h3 class="mb-2 text-lg font-semibold text-pk-dark">No openings listed right now</h3>
          <p class="mb-5 text-sm text-slate-600">New jobs come up often. Contact our team to know the latest openings or to register your interest.</p>
          <a href="{{ SafeRoute::url('contact') }}" class="btn btn-red">Contact Us</a>
        </div>
      @endif
    </div>
  </section>

  <x-site.cta title="Did Not Find Your Job?" text="Tell us what you are looking for and we will help." />
</div>
