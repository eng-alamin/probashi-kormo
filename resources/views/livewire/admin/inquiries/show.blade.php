@use('App\Support\Phone')
<div>
  <div class="page-head">
    <div>
      <h2>{{ $inquiry->full_name }}</h2>
      <p>Received {{ $inquiry->created_at->format('d M Y, h:i A') }}</p>
    </div>
    <a href="{{ route('admin.inquiries.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <div class="grid-2">
    <div class="glass panel">
      <div class="panel-title"><h5>Message</h5></div>
      <div class="detail-row"><span class="k">Phone</span><span><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a> · <a href="{{ Phone::whatsappUrl($inquiry->phone) }}" target="_blank" rel="noopener">WhatsApp</a></span></div>
      <div class="detail-row"><span class="k">Country</span><span>{{ $inquiry->country?->name ?? '—' }}</span></div>
      <div class="detail-row"><span class="k">Trade</span><span>{{ $inquiry->trade ?: '—' }}</span></div>
      <div class="detail-row" style="flex-direction:column;"><span class="k">Message</span><span class="note-text">{{ $inquiry->message }}</span></div>
    </div>

    <div class="glass panel">
      <div class="panel-title"><h5>Status</h5></div>
      <form wire:submit="save" novalidate>
        <div class="mb-3">
          <label class="form-label" for="status">Status</label>
          <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model="status" @cannot('inquiries.update') disabled @endcannot>
            @foreach ($statuses as $s)
              <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
          </select>
          @error('status')<div class="field-error show" role="alert">{{ $message }}</div>@enderror
        </div>

        @can('inquiries.update')
          <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Save</span>
            <span wire:loading wire:target="save">Saving…</span>
          </button>
        @endcan
        @can('inquiries.delete')
          <button type="button" class="btn btn-danger" wire:click="$set('confirmingDelete', true)">Delete</button>
        @endcan
      </form>
    </div>
  </div>

  <div class="modal-overlay {{ $confirmingDelete ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $confirmingDelete) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this inquiry?</h4>
      <p class="modal-sub">This cannot be undone.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="$set('confirmingDelete', false)">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
