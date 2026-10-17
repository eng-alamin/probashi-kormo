<div>
  <x-site.banner :title="$course->title" :crumbs="[['Training', route('training')], [$course->title, null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl gap-10 lg:grid-cols-[1.7fr_1fr]">
      <div class="space-y-10">
        <div class="reveal">
          <div class="mb-4 text-6xl">{{ $course->icon }}</div>
          <h2 class="mb-4 text-2xl font-bold text-pk-dark">{{ $course->title }} Course</h2>
          <p class="text-slate-600">{{ $course->description ?: $course->summary }}</p>
        </div>
        <x-site.checklist title="What You Will Learn" :text="$course->learn_points" />
        <x-site.checklist title="Where You Can Work Abroad" :text="$course->career_points" />
        <x-site.checklist title="Who Can Join" :text="$course->eligibility" />
        <x-site.steps title="Training Path" :steps="[
          ['Join', 'Register and choose your trade'],
          ['Learn', 'Theory and hands-on practice'],
          ['Test', 'Skill test and evaluation'],
          ['Get Placed', 'Apply for overseas jobs'],
        ]" />
      </div>
      <aside class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-lg shadow-green-900/10">
          <h3 class="mb-2 font-semibold text-pk-dark">Quick Info</h3>
          <x-site.info-row label="Course" :value="$course->title" />
          <x-site.info-row label="Duration" :value="$course->duration_weeks ? $course->duration_weeks.' weeks' : null" />
          <x-site.info-row label="Class days" :value="$course->class_days" />
          <x-site.info-row label="Course fee" :value="$course->fee ? '৳ '.number_format($course->fee) : null" />
          <x-site.info-row label="Certificate" :value="$course->certificate_info" />
        </div>
        <x-site.help-box apply="Join This Course" />
      </aside>
    </div>
  </section>

  <x-site.chips title="Other Courses" route="training.show" :items="$others->map(fn ($c) => [$c->icon.' '.$c->title, $c->slug])->all()" />
  <x-site.cta title="Ready to Start?" text="Ask about the next batch and how to join." />
</div>
