<div>
  <div class="page-head">
    <div><h2>Gallery</h2><p>Photos shown on the website gallery page</p></div>
    @can('gallery.create')
      <a href="{{ route('admin.gallery.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add photos</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search title or caption" aria-label="Search photos">
      </label>
      <select class="form-select" wire:model.live="category" aria-label="Filter by category">
        <option value="">All categories</option>
        @foreach ($categories as $c)
          <option value="{{ $c->value }}">{{ $c->label() }}</option>
        @endforeach
        <option value="none">No category</option>
      </select>
    </div>

    @if ($items->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-images"></i></div>
        <h6>No photos found</h6>
        <p>Add photos and they will appear on the website gallery page.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Photo</th>
              <th scope="col">Category</th>
              <th scope="col">Order</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($items as $item)
              <tr wire:key="photo-{{ $item->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <img class="cell-thumb" src="{{ \App\Support\ImageStore::url($item->image_path) }}" alt="{{ $item->title ?: 'Gallery photo' }}" loading="lazy">
                    <div>
                      <div class="cell-title">{{ $item->title ?: 'Untitled photo' }}</div>
                      @if ($item->caption)<div class="cell-sub">{{ \Illuminate\Support\Str::limit($item->caption, 60) }}</div>@endif
                    </div>
                  </div>
                </td>
                <td data-label="Category">{{ $item->category?->label() ?? '—' }}</td>
                <td data-label="Order">{{ $item->sort_order }}</td>
                <td data-label="Visible">
                  @can('gallery.update')
                    <button type="button" class="status-toggle" wire:click="toggleActive({{ $item->id }})" role="switch" aria-checked="{{ $item->is_active ? 'true' : 'false' }}" aria-label="Show this photo on website"><span class="switch {{ $item->is_active ? 'on' : '' }}"></span></button>
                  @else
                    {{ $item->is_active ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('gallery.update')
                      <a class="icon-action" href="{{ route('admin.gallery.edit', $item) }}" aria-label="Edit photo"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('gallery.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $item->id }})" aria-label="Delete photo"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $items->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this photo?</h4>
      <p class="modal-sub">The photo file is removed from the server too. To hide it temporarily, use the Visible switch instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
