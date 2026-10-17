@use('App\Enums\ApplicationStatus')
<div>
  <div class="page-head">
    <div><h2>Applications</h2><p>Job applications sent from the website</p></div>
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name or phone" aria-label="Search applications">
      </label>
      <select class="form-select" wire:model.live="status" aria-label="Filter by status">
        <option value="">All statuses</option>
        @foreach ($statuses as $s)
          <option value="{{ $s->value }}">{{ $s->label() }}</option>
        @endforeach
      </select>
      <select class="form-select" wire:model.live="country" aria-label="Filter by country">
        <option value="">All countries</option>
        @foreach ($countries as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    @if ($applications->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-person-lines-fill"></i></div>
        <h6>No applications found</h6>
        <p>New applications from the website will appear here.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Applicant</th>
              <th scope="col">Job / Trade</th>
              <th scope="col">Country</th>
              <th scope="col">Status</th>
              <th scope="col">Date</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($applications as $app)
              @php($pill = match ($app->status) {
                ApplicationStatus::New => 'status-trial',
                ApplicationStatus::Contacted => 'status-suspended',
                ApplicationStatus::Rejected => 'status-past',
                default => 'status-active',
              })
              <tr wire:key="app-{{ $app->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($app->full_name, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $app->full_name }}</div>
                      <div class="cell-sub">{{ $app->phone }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Job / Trade">{{ $app->jobPost?->title ?? ($app->trade ?: '—') }}</td>
                <td data-label="Country">{{ $app->country?->name ?? '—' }}</td>
                <td data-label="Status"><span class="status-pill {{ $pill }}">{{ $app->status->label() }}</span></td>
                <td data-label="Date">{{ $app->created_at->format('d M Y') }}</td>
                <td>
                  <div class="row-actions">
                    <a class="icon-action" href="{{ route('admin.applications.show', $app) }}" aria-label="View application of {{ $app->full_name }}"><i class="bi bi-eye" aria-hidden="true"></i></a>
                    @can('applications.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $app->id }})" aria-label="Delete application of {{ $app->full_name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $applications->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this application?</h4>
      <p class="modal-sub">This cannot be undone.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
