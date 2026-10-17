@use('App\Support\SafeRoute')
@use('App\Support\Site')
<div>
  <x-site.banner title="About Us" :crumbs="[['About', null]]" />
<!-- Who we are -->
<section class="py-16 md:py-20">
  <div class="mx-auto grid w-[92%] max-w-6xl items-center gap-12 md:grid-cols-2">
    <div class="reveal">
      <span class="eyebrow">Who We Are</span>
      <h2 class="title">Probashi Kormo Overseas &amp; Travels</h2>
      <p class="mb-4 text-slate-600">Probashi Kormo is a manpower recruiting agency. We prepare people with practical skill training in different fields and then send them abroad to work with trusted employers.</p>
      <p class="mb-4 text-slate-600">Our goal is simple: to give hard-working people a safe, legal and affordable way to build a better future overseas — with honest guidance at every step.</p>
      <a href="{{ SafeRoute::url('contact') }}" class="btn btn-red mt-2">Talk to Our Team</a>
    </div>
    <div class="reveal flex justify-center">
      <img src="{{ asset('site/img/logo.jpg') }}" alt="Probashi Kormo" class="w-64 rounded-full shadow-2xl md:w-80">
    </div>
  </div>
</section>

<!-- Mission / Vision / Values -->
<section class="bg-pk-bg py-16 md:py-20">
  <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
    <span class="eyebrow">Our Purpose</span>
    <h2 class="title">Mission, Vision &amp; Values</h2>
    <div class="mt-11 grid gap-6 md:grid-cols-3">
      <div class="card"><div class="mb-3 text-4xl">🎯</div><h3 class="mb-2 text-lg font-semibold text-pk-dark">Our Mission</h3><p class="text-[.95rem] text-slate-600">To train skilled workers and place them in safe, well-paid jobs abroad through a fully legal and transparent process.</p></div>
      <div class="card"><div class="mb-3 text-4xl">🌍</div><h3 class="mb-2 text-lg font-semibold text-pk-dark">Our Vision</h3><p class="text-[.95rem] text-slate-600">To become one of the most trusted names in overseas employment in Bangladesh.</p></div>
      <div class="card"><div class="mb-3 text-4xl">🤝</div><h3 class="mb-2 text-lg font-semibold text-pk-dark">Our Values</h3><p class="text-[.95rem] text-slate-600">Honesty, clear costs, respect for every worker, and care that continues even after departure.</p></div>
    </div>
  </div>
</section>

<!-- What we offer -->
<section class="py-16 md:py-20">
  <div class="mx-auto grid w-[92%] max-w-6xl items-center gap-12 md:grid-cols-2">
    <div class="reveal rounded-2xl bg-gradient-to-br from-pk-green to-pk-dark p-10 text-white shadow-xl">
      <h3 class="mb-3 text-xl font-semibold">Our Commitment</h3>
      <p class="mb-4">We never promise what we cannot deliver. Every candidate gets a clear explanation of the job, salary, contract, and total cost before they decide.</p>
      @if (Site::licenseNo())
      <p class="rounded-lg bg-white/10 p-3 text-sm">Recruiting License No: <b>{{ Site::licenseNo() }}</b></p>
      @endif
    </div>
    <div class="reveal">
      <span class="eyebrow">What We Offer</span>
      <h2 class="title">Support From First Step to Flight</h2>
      <ul class="space-y-2.5">
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">Job-oriented skill training in multiple trades</li>
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">Placement with verified overseas employers</li>
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">Passport, medical, visa and BMET guidance</li>
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">Pre-departure orientation and travel support</li>
        <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">Help and follow-up after you reach your destination</li>
      </ul>
    </div>
  </div>
</section>

<!-- Why choose us -->
<section class="bg-pk-bg py-16 md:py-20">
  <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
    <span class="eyebrow">Why Choose Us</span>
    <h2 class="title">What Makes Us Different</h2>
    <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <div class="card text-center"><div class="mb-3 text-4xl">✅</div><h3 class="mb-1 font-semibold text-pk-dark">Legal &amp; Safe</h3><p class="text-sm text-slate-600">Every step follows government rules.</p></div>
      <div class="card text-center"><div class="mb-3 text-4xl">💰</div><h3 class="mb-1 font-semibold text-pk-dark">Clear Costs</h3><p class="text-sm text-slate-600">No hidden fees or surprises.</p></div>
      <div class="card text-center"><div class="mb-3 text-4xl">🛠️</div><h3 class="mb-1 font-semibold text-pk-dark">Skilled Workers</h3><p class="text-sm text-slate-600">Hands-on training before you fly.</p></div>
      <div class="card text-center"><div class="mb-3 text-4xl">📞</div><h3 class="mb-1 font-semibold text-pk-dark">Always Reachable</h3><p class="text-sm text-slate-600">Friendly support when you need it.</p></div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="bg-gradient-to-r from-pk-red to-red-800 py-16 text-center text-white">
  <div class="reveal mx-auto w-[92%] max-w-6xl">
    <h2 class="mb-2 text-3xl font-bold md:text-4xl">Ready to Work Abroad?</h2>
    <p class="mb-6 text-red-100">Visit our office or send us a message today.</p>
    <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line">Contact Us</a>
  </div>
</section>
</div>
