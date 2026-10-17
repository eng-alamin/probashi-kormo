<div>
  <div class="page-head">
    <div><h2>Countries</h2><p>Destinations shown on the website</p></div>
    @can('countries.create')
      <a href="{{ route('admin.countries.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New country</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search country" aria-label="Search countries">
      </label>
    </div>

    @if ($countries->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-globe2"></i></div>
        <h6>No countries found</h6>
        <p>Add a country to show it on the website.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Country</th>
              <th scope="col">Order</th>
              <th scope="col">Jobs</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($countries as $country)
              <tr wire:key="country-{{ $country->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" style="font-size:1.1rem;" aria-hidden="true">{{ $country->flag ?: mb_strtoupper(mb_substr($country->name, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $country->name }}</div>
                      <div class="cell-sub">/countries/{{ $country->slug }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Order">{{ $country->sort_order }}</td>
                <td data-label="Jobs">{{ $country->job_posts_count }}</td>
                <td data-label="Visible">
                  @can('countries.update')
                    <button type="button" class="status-toggle" wire:click="toggleActive({{ $country->id }})" role="switch" aria-checked="{{ $country->is_active ? 'true' : 'false' }}" aria-label="Show {{ $country->name }} on website"><span class="switch {{ $country->is_active ? 'on' : '' }}"></span></button>
                  @else
                    {{ $country->is_active ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('countries.update')
                      <a class="icon-action" href="{{ route('admin.countries.edit', $country) }}" aria-label="Edit {{ $country->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('countries.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $country->id }})" aria-label="Delete {{ $country->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this country?</h4>
      <p class="modal-sub">A country that still has jobs cannot be deleted. To remove it from the website without deleting, use the Visible switch.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
