@use('App\Support\SafeRoute')
@use('App\Support\Site')
@php
  $applyUrl = SafeRoute::exists('apply') ? route('apply', ['job' => $job->slug]) : '#';
@endphp
<div>
  <x-site.banner :title="$job->title" :crumbs="[['Jobs', route('jobs')], [$job->title, null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl gap-10 lg:grid-cols-[1.7fr_1fr]">
      <div class="space-y-10">
        @if ($closed)
          <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">This opening is closed (last date was {{ $job->deadline->format('d M Y') }}). Contact us to ask about similar jobs.</div>
        @endif

        <div class="reveal">
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $job->title }}@if ($job->country) – {{ $job->country->name }}@endif</h2>
          <p class="whitespace-pre-line text-slate-600">{{ $job->description ?: $job->summary }}</p>
        </div>

        <x-site.checklist title="Requirements" :text="$job->requirements" />
        <x-site.checklist title="Salary and Benefits" :text="$job->benefits" />

        <x-site.steps title="How to Apply" :steps="[
          ['Apply', 'Submit the online form'],
          ['Selection', 'Interview or trade test'],
          ['Process', 'Visa and medical'],
          ['Fly', 'Travel and start work'],
        ]" />
      </div>

      <aside class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-lg shadow-green-900/10">
          <h3 class="mb-2 font-semibold text-pk-dark">Quick Info</h3>
          <x-site.info-row label="Position" :value="$job->title" />
          <x-site.info-row label="Country" :value="$job->country?->name" />
          <x-site.info-row label="Category" :value="$job->category" />
          <x-site.info-row label="Salary" :value="$salary ?? 'As per contract'" />
          <x-site.info-row label="Vacancies" :value="$job->vacancies" />
          <x-site.info-row label="Last date" :value="$job->deadline?->format('d M Y')" />
        </div>

        @if ($closed)
          <div class="rounded-2xl bg-gradient-to-br from-pk-green to-pk-dark p-6 text-white shadow-xl">
            <h3 class="mb-2 text-lg font-semibold">Applications closed</h3>
            <p class="mb-4 text-sm text-green-100">Ask us about other openings.@if (Site::phone()) 📞 <a href="{{ Site::phoneHref() }}" class="font-semibold hover:underline">{{ Site::phone() }}</a>@endif</p>
            <a href="{{ SafeRoute::url('contact') }}" class="btn btn-red block text-center">Contact Us</a>
          </div>
        @else
          <div class="rounded-2xl bg-gradient-to-br from-pk-green to-pk-dark p-6 text-white shadow-xl">
            <h3 class="mb-2 text-lg font-semibold">Need help?</h3>
            <p class="mb-4 text-sm text-green-100">Our team will explain everything clearly.@if (Site::phone()) 📞 <a href="{{ Site::phoneHref() }}" class="font-semibold hover:underline">{{ Site::phone() }}</a>@endif</p>
            <a href="{{ $applyUrl }}" class="btn btn-red mb-3 block text-center">Apply for This Job</a>
            <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line block text-center">Contact Us</a>
          </div>
        @endif
      </aside>
    </div>
  </section>

  <x-site.cta title="Not the Right Job?" text="Tell us your skills and we will find a better match." />
</div>
