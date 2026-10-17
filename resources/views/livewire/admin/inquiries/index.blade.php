@use('App\Enums\InquiryStatus')
<div>
  <div class="page-head">
    <div><h2>Inquiries</h2><p>Messages sent from the contact page</p></div>
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name, phone or message" aria-label="Search inquiries">
      </label>
      <select class="form-select" wire:model.live="status" aria-label="Filter by status">
        <option value="">All statuses</option>
        @foreach ($statuses as $s)
          <option value="{{ $s->value }}">{{ $s->label() }}</option>
        @endforeach
      </select>
    </div>

    @if ($inquiries->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-inbox-fill"></i></div>
        <h6>No inquiries found</h6>
        <p>Messages from the contact page will appear here.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Sender</th>
              <th scope="col">Message</th>
              <th scope="col">Country</th>
              <th scope="col">Status</th>
              <th scope="col">Date</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($inquiries as $inq)
              @php($pill = match ($inq->status) {
                InquiryStatus::New => 'status-trial',
                InquiryStatus::Read => 'status-suspended',
                default => 'status-active',
              })
              <tr wire:key="inq-{{ $inq->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($inq->full_name, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $inq->full_name }}</div>
                      <div class="cell-sub">{{ $inq->phone }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Message">{{ \Illuminate\Support\Str::limit($inq->message, 60) }}</td>
                <td data-label="Country">{{ $inq->country?->name ?? '—' }}</td>
                <td data-label="Status"><span class="status-pill {{ $pill }}">{{ $inq->status->label() }}</span></td>
                <td data-label="Date">{{ $inq->created_at->format('d M Y') }}</td>
                <td>
                  <div class="row-actions">
                    <a class="icon-action" href="{{ route('admin.inquiries.show', $inq) }}" aria-label="View message from {{ $inq->full_name }}"><i class="bi bi-eye" aria-hidden="true"></i></a>
                    @can('inquiries.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $inq->id }})" aria-label="Delete message from {{ $inq->full_name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $inquiries->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this inquiry?</h4>
      <p class="modal-sub">This cannot be undone.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
