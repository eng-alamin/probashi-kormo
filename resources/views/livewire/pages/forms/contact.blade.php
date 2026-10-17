@use('App\Support\Site')
<div>
  <x-site.banner title="Contact Us" :crumbs="[['Contact', null]]" />

  <section class="py-16 md:py-20">
    <div class="mx-auto grid w-[92%] max-w-6xl gap-10 lg:grid-cols-[1fr_1.1fr]">
      <div>
        <span class="eyebrow">Get in Touch</span>
        <h2 class="title">We Are Happy to Help</h2>
        <p class="lead mb-6">Visit our office, call us, or send a message. We will explain jobs, requirements and costs clearly.</p>
        <div class="space-y-4">
          @php
            $cards = [
              ['📍', 'Office Address', Site::address()],
              ['📞', 'Phone', Site::phone()],
              ['✉️', 'Email', Site::email()],
              ['🕒', 'Office Hours', Site::hours()],
            ];
          @endphp
          @foreach ($cards as [$icon, $heading, $value])
            @if (filled($value))
              <div class="flex gap-4 rounded-2xl bg-white p-5 shadow-lg shadow-green-900/10">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-pk-green/10 text-2xl">{{ $icon }}</div>
                <div><h3 class="font-semibold text-pk-dark">{{ $heading }}</h3><p class="text-sm text-slate-600">{{ $value }}</p></div>
              </div>
            @endif
          @endforeach
        </div>
        <a href="{{ config('site.facebook') }}" target="_blank" rel="noopener" class="btn btn-green mt-6">Visit Our Facebook Page</a>
      </div>

      <div class="rounded-2xl bg-white p-6 shadow-xl shadow-green-900/10 md:p-8">
        @if ($sent)
          <div class="py-6 text-center">
            <div class="mb-3 text-6xl">✅</div>
            <h3 class="mb-2 text-xl font-semibold text-pk-dark">Message Sent</h3>
            <p class="text-sm text-slate-600">Thank you. Our team will contact you soon on the phone number you gave.</p>
          </div>
        @else
          <h3 class="mb-1 text-xl font-semibold text-pk-dark">Send Us a Message</h3>
          <p class="mb-5 text-sm text-slate-500">We will reply on your phone number.</p>
          <form wire:submit="submit" class="space-y-4">
            <x-site.field label="Full Name" model="form.full_name" required placeholder="Your name" autocomplete="name" />
            <x-site.field label="Phone Number" model="form.phone" type="tel" required placeholder="01XXXXXXXXX" autocomplete="tel" />
            <div class="grid gap-4 sm:grid-cols-2">
              <x-site.select label="Preferred Country" model="form.country_id" required>
                <option value="">Select country</option>
                @foreach ($countries as $country)
                  <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
                <option value="other">Other / Not sure</option>
              </x-site.select>
              <x-site.field label="Trade / Job" model="form.trade" placeholder="e.g. Electrician" />
            </div>
            <x-site.textarea label="Message" model="form.message" rows="4" required placeholder="Write your question here" />

            <x-site.honeypot />

            <button type="submit" class="btn btn-red w-full" wire:loading.attr="disabled" wire:target="submit">
              <span wire:loading.remove wire:target="submit">Send Message</span>
              <span wire:loading wire:target="submit">Sending…</span>
            </button>
          </form>
        @endif
      </div>
    </div>
  </section>

  @if ($mapUrl)
    <section class="bg-pk-bg py-16 md:py-20">
      <div class="mx-auto w-[92%] max-w-6xl text-center">
        <span class="eyebrow">Find Us</span>
        <h2 class="title mb-8">Our Location</h2>
        <iframe src="{{ $mapUrl }}" title="Our location" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="h-80 w-full rounded-2xl border-0 shadow-xl"></iframe>
      </div>
    </section>
  @endif
</div>
