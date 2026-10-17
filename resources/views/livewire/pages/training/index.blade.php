@use('App\Support\SafeRoute')
<div>
  <x-site.banner title="Skill Training" :crumbs="[['Training', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl items-center gap-12 md:grid-cols-2">
      <div class="reveal">
        <span class="eyebrow">Train With Us</span>
        <h2 class="title">Skills That Get You Hired Abroad</h2>
        <p class="mb-4 text-slate-600">Employers overseas prefer trained workers. Our practical courses prepare you with the skills, discipline and confidence needed to succeed in your job.</p>
        <ul class="space-y-2.5">
          @foreach (['Short, job-focused courses', 'Practical work with real tools', 'Direct link to overseas job placement'] as $point)
            <li class="relative pl-7 before:absolute before:left-0 before:text-pk-red before:content-['✔']">{{ $point }}</li>
          @endforeach
        </ul>
        <a href="{{ SafeRoute::url('contact') }}" class="btn btn-red mt-5">Join a Course</a>
      </div>
      <div class="reveal rounded-2xl bg-gradient-to-br from-pk-green to-pk-dark p-10 text-white shadow-xl">
        <h3 class="mb-2 text-xl font-semibold">Not sure which trade?</h3>
        <p>Tell us your experience and interest. Our team will suggest the trade with the best job chances for you.</p>
        <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line mt-5">Ask Our Team</a>
      </div>
    </div>
  </section>

  <section class="bg-pk-bg py-16 md:py-20">
    <div class="reveal mx-auto w-[92%] max-w-6xl text-center">
      <span class="eyebrow">Our Courses</span>
      <h2 class="title">Choose Your Trade</h2>
      <div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($courses as $course)
          <a href="{{ route('training.show', $course) }}" class="card block">
            <div class="mb-3 text-4xl">{{ $course->icon }}</div>
            <h3 class="mb-2 text-lg font-semibold text-pk-dark">{{ $course->title }}</h3>
            <p class="text-[.95rem] text-slate-600">{{ $course->summary }}</p>
            <span class="mt-3 inline-block text-sm font-semibold text-pk-red">View course →</span>
          </a>
        @empty
          <p class="col-span-full text-slate-500">Courses will be listed here soon.</p>
        @endforelse
      </div>
    </div>
  </section>

  <x-site.cta title="Ready to Learn a Skill?" text="Talk to our team about the next batch." />
</div>
