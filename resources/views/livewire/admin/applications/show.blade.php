@use('App\Support\Phone')
<div>
  <div class="page-head">
    <div>
      <h2>{{ $application->full_name }}</h2>
      <p>Applied {{ $application->created_at->format('d M Y, h:i A') }}</p>
    </div>
    <a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <div class="grid-2">
    <div class="glass panel">
      <div class="panel-title"><h5>Applicant details</h5></div>
      <div class="detail-row"><span class="k">Phone</span><span><a href="tel:{{ $application->phone }}">{{ $application->phone }}</a> · <a href="{{ Phone::whatsappUrl($application->phone) }}" target="_blank" rel="noopener">WhatsApp</a></span></div>
      <div class="detail-row"><span class="k">Age</span><span>{{ $application->age ?? '—' }}</span></div>
      <div class="detail-row"><span class="k">District</span><span>{{ $application->district ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">Passport</span><span>{{ $application->passport_status->label() }}</span></div>
      <div class="detail-row"><span class="k">Education</span><span>{{ $application->education ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">Experience</span><span>{{ $application->experience ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">Job</span><span>{{ $application->jobPost?->title ?? '—' }}</span></div>
      <div class="detail-row"><span class="k">Trade</span><span>{{ $application->trade ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">Country</span><span>{{ $application->country?->name ?? '—' }}</span></div>
      @if ($application->message)
        <div class="detail-row" style="flex-direction:column;"><span class="k">Message</span><span class="note-text">{{ $application->message }}</span></div>
      @endif
    </div>

    <div class="glass panel">
      <div class="panel-title"><h5>Follow-up</h5></div>
      <form wire:submit="save" novalidate>
        <div class="mb-3">
          <label class="form-label" for="status">Status</label>
          <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model="status" @cannot('applications.update') disabled @endcannot>
            @foreach ($statuses as $s)
              <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
          </select>
          @error('status')<div class="field-error show" role="alert">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="admin_note">Internal note</label>
          <textarea id="admin_note" rows="5" class="form-control @error('admin_note') is-invalid @enderror" wire:model="admin_note" maxlength="2000" @cannot('applications.update') disabled @endcannot></textarea>
          @error('admin_note')<div class="field-error show" role="alert">{{ $message }}</div>@enderror
        </div>

        @if ($application->handledBy)
          <p class="cell-sub" style="margin-bottom:14px;">Last handled by {{ $application->handledBy->name }}</p>
        @endif

        @can('applications.update')
          <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Save</span>
            <span wire:loading wire:target="save">Saving…</span>
          </button>
        @endcan
        @can('applications.delete')
          <button type="button" class="btn btn-danger" wire:click="$set('confirmingDelete', true)">Delete</button>
        @endcan
      </form>
    </div>
  </div>

  <div class="modal-overlay {{ $confirmingDelete ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $confirmingDelete) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this application?</h4>
      <p class="modal-sub">This cannot be undone.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="$set('confirmingDelete', false)">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
