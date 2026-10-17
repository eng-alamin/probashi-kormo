@use('App\Support\SafeRoute')
<div>
  <x-site.banner title="Our Services" :crumbs="[['Services', null]]" />

  <section class="py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">What We Do</span>
      <h2 class="title">Everything You Need to Work Abroad</h2>
      <p class="lead mx-auto">One agency, one team — from your first training class to your flight.</p>
      <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($services as $service)
          <a href="{{ route('services.show', $service) }}" class="card block">
            <div class="mb-3 text-4xl">{{ $service->icon }}</div>
            <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $service->title }}</h3>
            <p class="text-[.95rem] text-slate-600">{{ $service->summary }}</p>
            <span class="mt-3 inline-block text-sm font-semibold text-pk-red">Learn more →</span>
          </a>
        @empty
          <p class="col-span-full text-slate-500">Services will be listed here soon.</p>
        @endforelse
      </div>
    </div>
  </section>

  <section class="bg-pk-bg py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">How It Works</span>
      <h2 class="title">Simple 4-Step Process</h2>
      <div class="mt-11">
        <x-site.steps :steps="[
          ['Registration', 'Submit documents and choose your trade.'],
          ['Training', 'Complete skill training at our center.'],
          ['Visa & Medical', 'We process visa, medical and clearance.'],
          ['Departure', 'Fly to your job with full support.'],
        ]" />
      </div>
    </div>
  </section>

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-3xl">
      <div class="mb-10 text-center">
        <span class="eyebrow">FAQ</span>
        <h2 class="title">Frequently Asked Questions</h2>
      </div>
      <x-site.faq title="" :items="[
        ['What documents do I need to start?', 'Usually a valid passport, national ID, passport-size photos and educational or skill certificates if you have them. Our team will confirm the exact list for your job.'],
        ['How long does the whole process take?', 'It depends on the country, the job and visa processing time. We will give you an honest estimate before you begin.'],
        ['Is training required before going abroad?', 'For most skilled jobs, yes. Training improves your chances of selection and helps you work confidently from day one.'],
        ['What is the total cost?', 'The cost depends on the country and job. We explain every cost clearly in advance, with no hidden charges.'],
      ]" />
    </div>
  </section>

  <x-site.cta title="Have a Question? Ask Us." text="Our team will explain jobs, requirements and costs clearly." />
</div>
