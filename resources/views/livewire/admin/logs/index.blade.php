@use('App\Enums\ActivityEvent')
<div>
  <div class="page-head">
    <div><h2>Activity Log</h2><p>Who did what in the admin panel. Old lines are removed automatically after {{ config('activity.retention_days') }} days.</p></div>
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name or description" aria-label="Search activity">
      </label>
      <select class="form-select" wire:model.live="kind" aria-label="Filter by event">
        <option value="">All events</option>
        @foreach ($events as $case)
          <option value="{{ $case->value }}">{{ $case->label() }}</option>
        @endforeach
      </select>
      <select class="form-select" wire:model.live="staff" aria-label="Filter by staff member">
        <option value="">All staff</option>
        @foreach ($staffUsers as $person)
          <option value="{{ $person->id }}">{{ $person->name }}</option>
        @endforeach
      </select>
    </div>

    @if ($logs->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-terminal-fill"></i></div>
        <h6>No activity found</h6>
        <p>Changes made in the admin panel will appear here.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">When</th>
              <th scope="col">Staff</th>
              <th scope="col">Event</th>
              <th scope="col">What happened</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($logs as $log)
              @php($type = ActivityEvent::tryFrom($log->event))
              <tr wire:key="log-{{ $log->id }}">
                <td data-label="When">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                <td data-label="Staff" class="cell-main-td">
                  <div class="cell-title">{{ $log->user?->name ?? $log->actor_name ?? 'System' }}</div>
                  @if ($log->ip_address)<div class="cell-sub">{{ $log->ip_address }}</div>@endif
                </td>
                <td data-label="Event"><span class="status-pill {{ $type?->pill() ?? 'status-suspended' }}">{{ $type?->label() ?? ucfirst(str_replace('_', ' ', $log->event)) }}</span></td>
                <td data-label="What happened">{{ $log->description }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $logs->links('pagination.admin') }}
    @endif
  </div>
</div>
