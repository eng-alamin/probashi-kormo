<div>
  <div class="page-head">
    <div><h2>Jobs</h2><p>Openings shown on the website</p></div>
    @can('jobs.create')
      <a href="{{ route('admin.jobs.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New job</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search job title" aria-label="Search jobs">
      </label>
      <select class="form-select" wire:model.live="country" aria-label="Filter by country">
        <option value="">All countries</option>
        @foreach ($countries as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
      <select class="form-select" wire:model.live="state" aria-label="Filter by state">
        <option value="">All jobs</option>
        <option value="open">Open</option>
        <option value="closed">Closed (deadline passed)</option>
        <option value="inactive">Hidden</option>
      </select>
    </div>

    @if ($jobs->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-briefcase-fill"></i></div>
        <h6>No jobs found</h6>
        <p>Add a job and it will appear on the website jobs page.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Job</th>
              <th scope="col">Country</th>
              <th scope="col">Deadline</th>
              <th scope="col">Applications</th>
              <th scope="col">Status</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($jobs as $job)
              @php($expired = $job->deadline !== null && $job->deadline->lt(today()))
              <tr wire:key="job-{{ $job->id }}">
                <td class="cell-main-td">
                  <div class="cell-title">{{ $job->title }}@if ($job->is_featured) <i class="bi bi-star-fill" style="color:var(--citrus); font-size:.75rem;" title="Featured" aria-label="Featured"></i>@endif</div>
                  <div class="cell-sub">{{ $job->category ?: '—' }}</div>
                </td>
                <td data-label="Country">{{ $job->country?->name ?? '—' }}</td>
                <td data-label="Deadline">{{ $job->deadline?->format('d M Y') ?? '—' }}</td>
                <td data-label="Applications">{{ $job->applications_count }}</td>
                <td data-label="Status">
                  @if (! $job->is_active)
                    <span class="status-pill status-suspended">Hidden</span>
                  @elseif ($expired)
                    <span class="status-pill status-past">Closed</span>
                  @else
                    <span class="status-pill status-active">Open</span>
                  @endif
                </td>
                <td data-label="Visible">
                  @can('jobs.update')
                    <button type="button" class="status-toggle" wire:click="toggleActive({{ $job->id }})" role="switch" aria-checked="{{ $job->is_active ? 'true' : 'false' }}" aria-label="Show {{ $job->title }} on website"><span class="switch {{ $job->is_active ? 'on' : '' }}"></span></button>
                  @else
                    {{ $job->is_active ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('jobs.update')
                      <button type="button" class="icon-action" wire:click="toggleFeatured({{ $job->id }})" aria-pressed="{{ $job->is_featured ? 'true' : 'false' }}" aria-label="Toggle featured for {{ $job->title }}"><i class="bi {{ $job->is_featured ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i></button>
                      <a class="icon-action" href="{{ route('admin.jobs.edit', $job) }}" aria-label="Edit {{ $job->title }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('jobs.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $job->id }})" aria-label="Delete {{ $job->title }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{ $jobs->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this job?</h4>
      <p class="modal-sub">It will disappear from the website. Applications already received are kept, only their link to this job is removed. To hide a job temporarily, use the Visible switch instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
