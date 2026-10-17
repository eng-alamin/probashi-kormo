@use('App\Support\Lines')
@use('App\Support\SafeRoute')
<div>
  <x-site.banner :title="$country->name" :crumbs="[['Countries', route('countries')], [$country->name, null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl gap-10 lg:grid-cols-[1.7fr_1fr]">
      <div class="space-y-10">
        <div class="reveal">
          <div class="mb-4 text-6xl">{{ $country->flag }}</div>
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">Working in {{ $country->name }}</h2>
          <p class="text-slate-600">{{ $country->description ?: $country->summary }}</p>
        </div>

        <x-site.checklist title="Common Job Sectors" :text="$country->job_sectors" />

        <div class="reveal">
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">Documents Usually Needed</h2>
          <ul class="space-y-2.5">
            @foreach (['Valid passport', 'National ID card', 'Recent passport-size photos', 'Educational or skill certificates, if you have them', 'Medical report and clearance, as required', 'Any other document needed for your specific job'] as $doc)
              <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">{{ $doc }}</li>
            @endforeach
          </ul>
          <p class="mt-3 text-sm text-slate-500">Our team confirms the exact list for your job and country.</p>
        </div>

        @if (filled($country->extra_info))
          <div class="reveal rounded-2xl bg-pk-bg p-6">
            <h3 class="mb-2 font-semibold text-pk-dark">Country-specific information</h3>
            <p class="whitespace-pre-line text-sm text-slate-600">{{ $country->extra_info }}</p>
          </div>
        @endif

        <x-site.steps title="How It Works" :steps="[
          ['Choose Country', 'Tell us your preferred destination and job.'],
          ['Check Demand', 'We share jobs and requirements.'],
          ['Process Documents', 'Visa, medical and clearance with our help.'],
          ['Fly & Work', 'Travel with full support.'],
        ]" />

        <div class="reveal">
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">Frequently Asked Questions</h2>
          <div class="space-y-4">
            <details class="group rounded-xl bg-white p-5 shadow-lg shadow-green-900/10">
              <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-pk-dark">Which jobs are available now?<span class="text-pk-red transition group-open:rotate-45">+</span></summary>
              <p class="mt-3 text-sm text-slate-600">Openings change often. Please check our <a href="{{ SafeRoute::url('jobs') }}" class="font-semibold text-pk-red">Jobs page</a> or contact us for the latest list.</p>
            </details>
            <details class="group rounded-xl bg-white p-5 shadow-lg shadow-green-900/10">
              <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-pk-dark">Do I need training first?<span class="text-pk-red transition group-open:rotate-45">+</span></summary>
              <p class="mt-3 text-sm text-slate-600">For most skilled jobs, trained workers are preferred. See our <a href="{{ SafeRoute::url('training') }}" class="font-semibold text-pk-red">training courses</a>.</p>
            </details>
            <details class="group rounded-xl bg-white p-5 shadow-lg shadow-green-900/10">
              <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-pk-dark">How much will it cost?<span class="text-pk-red transition group-open:rotate-45">+</span></summary>
              <p class="mt-3 text-sm text-slate-600">The cost depends on the job. We explain every cost clearly before you begin, with no hidden charges.</p>
            </details>
          </div>
        </div>
      </div>

      <aside class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-lg shadow-green-900/10">
          <h3 class="mb-2 font-semibold text-pk-dark">Quick Info</h3>
          <x-site.info-row label="Country" :value="$country->name" />
          <x-site.info-row label="Job sectors" :value="count(Lines::split($country->job_sectors)) ? count(Lines::split($country->job_sectors)).' main sectors' : null" />
          <x-site.info-row label="Salary" value="As per contract" />
          <x-site.info-row label="Contract" :value="$country->contract_info" />
        </div>
        <x-site.help-box />
      </aside>
    </div>
  </section>

  <x-site.chips title="Other Countries" route="countries.show" :items="$others->map(fn ($c) => [$c->flag.' '.$c->name, $c->slug])->all()" />
  <x-site.cta :title="'Find Your Job in '.$country->name" text="Ask us about the latest openings." />
</div>
