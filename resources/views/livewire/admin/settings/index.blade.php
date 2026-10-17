<div>
  <div class="page-head">
    <div><h2>Settings</h2><p>Contact details and home page text shown on the public website</p></div>
  </div>

  <form wire:submit="save" novalidate>
    <fieldset @disabled(! $canEdit) style="border:0;padding:0;margin:0;min-width:0">
      <div class="glass panel" style="margin-bottom:1.25rem">
        <h3 style="margin:0 0 .25rem">Contact details</h3>
        <p class="form-hint" style="margin-bottom:1rem">Shown in the website header, footer and Contact page. Leave a box empty to use the value from the server's .env file.</p>
        <div class="form-grid">
          <x-admin.field label="Phone" model="phone" placeholder="+880 1712-345678" />
          <x-admin.field label="Email" model="email" type="email" placeholder="info@yourdomain.com" />
          <x-admin.field label="Office address" model="address" />
          <x-admin.field label="WhatsApp number" model="whatsapp" placeholder="8801712345678" hint="Country code first, no + sign. The WhatsApp button is hidden until this is filled." />
          <x-admin.field label="Office hours" model="hours" placeholder="Sat–Thu, 9am–6pm" />
          <x-admin.field label="Recruiting license no." model="license_no" hint="Shown on the About page when filled." />
          <x-admin.field label="Google Maps embed link" model="map_embed" :span="true" hint="Google Maps > Share > Embed a map > copy only the src=&quot;...&quot; link." />
        </div>
      </div>

      <div class="glass panel">
        <h3 style="margin:0 0 .25rem">Home page</h3>
        <p class="form-hint" style="margin-bottom:1rem">The big heading, short text and the four numbers under it. Only put numbers that are true and that you can prove.</p>
        <div class="form-grid">
          <x-admin.field label="Heading" model="home_title" :required="true" maxlength="60" />
          <x-admin.field label="Heading (red part)" model="home_highlight" maxlength="60" hint="Optional second line shown in a lighter colour." />
          <x-admin.textarea label="Short text" model="home_text" :required="true" :rows="3" hint="Up to 300 characters." />

          @foreach ([1, 2, 3, 4] as $n)
            <x-admin.field :label="'Number '.$n" :model="'stat'.$n.'_value'" :required="true" maxlength="12" placeholder="500+" />
            <x-admin.field :label="'Label '.$n" :model="'stat'.$n.'_label'" :required="true" maxlength="40" placeholder="Workers Sent" />
          @endforeach
        </div>
      </div>
    </fieldset>

    @if ($canEdit)
      <div class="form-actions">
        <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
          <span wire:loading.remove wire:target="save">Save settings</span>
          <span wire:loading wire:target="save">Saving…</span>
        </button>
      </div>
    @else
      <p class="form-hint">You can view these settings but not change them.</p>
    @endif
  </form>
</div>
