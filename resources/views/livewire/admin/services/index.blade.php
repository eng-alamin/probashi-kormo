<div>
  <div class="page-head">
    <div><h2>Services</h2><p>Services shown on the website</p></div>
    @can('services.create')
      <a href="{{ route('admin.services.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New service</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search service" aria-label="Search services">
      </label>
    </div>

    @if ($services->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-tools"></i></div>
        <h6>No services found</h6>
        <p>Add a service to show it on the website.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Service</th>
              <th scope="col">Order</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($services as $service)
              <tr wire:key="service-{{ $service->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" style="font-size:1.1rem;" aria-hidden="true">{{ $service->icon ?: mb_strtoupper(mb_substr($service->title, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $service->title }}</div>
                      <div class="cell-sub">/services/{{ $service->slug }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Order">{{ $service->sort_order }}</td>
                <td data-label="Visible">
                  @can('services.update')
                    <button type="button" class="status-toggle" wire:click="toggleActive({{ $service->id }})" role="switch" aria-checked="{{ $service->is_active ? 'true' : 'false' }}" aria-label="Show {{ $service->title }} on website"><span class="switch {{ $service->is_active ? 'on' : '' }}"></span></button>
                  @else
                    {{ $service->is_active ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('services.update')
                      <a class="icon-action" href="{{ route('admin.services.edit', $service) }}" aria-label="Edit {{ $service->title }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('services.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $service->id }})" aria-label="Delete {{ $service->title }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
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
      <h4 id="delTitle">Delete this service?</h4>
      <p class="modal-sub">It will be removed from the website. To hide it temporarily, use the Visible switch instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
