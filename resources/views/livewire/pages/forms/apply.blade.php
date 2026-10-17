@use('App\Support\SafeRoute')
@use('App\Support\Site')
<div>
  <x-site.banner title="Apply Online" :crumbs="[['Apply', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto w-[92%] max-w-3xl">
      @if ($sent)
        <div class="rounded-2xl bg-white p-8 text-center shadow-xl shadow-green-900/10 md:p-10">
          <div class="mb-3 text-6xl">✅</div>
          <h2 class="title">Thank You!</h2>
          <p class="mb-2 text-slate-600">We have received your application. Our team will contact you on the phone number you gave.</p>
          @if ($reference)
            <p class="mb-5 text-sm text-slate-500">Your reference: <b class="text-pk-dark">{{ $reference }}</b></p>
          @endif
          <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ SafeRoute::url('jobs') }}" class="btn btn-green">See Jobs</a>
            @if (Site::whatsappUrl())
              <a href="{{ Site::whatsappUrl() }}" target="_blank" rel="noopener" class="btn btn-red">Message us on WhatsApp</a>
            @endif
          </div>
        </div>
      @else
        <div class="mb-8 text-center">
          <span class="eyebrow">Start Here</span>
          <h2 class="title">Job Application Form</h2>
          <p class="lead mx-auto">Fill in your details and send. Our team will call you to explain the next steps.</p>
        </div>

        <form wire:submit="submit" class="space-y-5 rounded-2xl bg-white p-6 shadow-xl shadow-green-900/10 md:p-8">
          <div class="grid gap-5 sm:grid-cols-2">
            <x-site.field label="Full Name" model="form.full_name" required placeholder="Your name" autocomplete="name" />
            <x-site.field label="Phone Number" model="form.phone" type="tel" required placeholder="01XXXXXXXXX" autocomplete="tel" />
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <x-site.field label="Age" model="form.age" type="number" required placeholder="e.g. 25" inputmode="numeric" />
            <x-site.field label="District / Address" model="form.district" required placeholder="Your district" />
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <x-site.select label="Do you have a passport?" model="form.passport_status" required>
              <option value="">Select</option>
              @foreach ($passportOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </x-site.select>
            <x-site.select label="Education" model="form.education">
              <option value="">Select</option>
              @foreach ($education as $level)
                <option value="{{ $level }}">{{ $level }}</option>
              @endforeach
            </x-site.select>
          </div>

          <div class="grid gap-5 sm:grid-cols-2">
            <x-site.select label="Preferred Country" model="form.country_id" required>
              <option value="">Select country</option>
              @foreach ($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
              @endforeach
              <option value="other">Other / Not sure</option>
            </x-site.select>
            <x-site.field label="Experience (years)" model="form.experience" placeholder="e.g. 2 years" />
          </div>

          <x-site.field label="Job / Trade" model="form.trade" required placeholder="e.g. Electrician" />
          <x-site.textarea label="Message" model="form.message" rows="3" placeholder="Anything else you want to tell us" />

          <x-site.honeypot />

          <button type="submit" class="btn btn-red w-full" wire:loading.attr="disabled" wire:target="submit">
            <span wire:loading.remove wire:target="submit">Send Application</span>
            <span wire:loading wire:target="submit">Sending…</span>
          </button>
          <p class="text-center text-xs text-slate-500">By sending, you agree to our <a href="{{ SafeRoute::url('privacy-policy') }}" class="text-pk-red hover:underline">Privacy Policy</a>. Never share your passport number on this form.</p>
        </form>
      @endif
    </div>
  </section>
</div>
