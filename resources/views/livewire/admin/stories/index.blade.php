<div>
  <div class="page-head">
    <div><h2>Success Stories</h2><p>Worker stories shown on the website</p></div>
    @can('stories.create')
      <a href="{{ route('admin.stories.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New story</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name or trade" aria-label="Search stories">
      </label>
    </div>

    @if ($stories->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-trophy-fill"></i></div>
        <h6>No stories found</h6>
        <p>Add a story only with the worker's permission.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Person</th>
              <th scope="col">Country</th>
              <th scope="col">Order</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($stories as $story)
              <tr wire:key="story-{{ $story->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    @if ($story->photo_path)
                      <img class="cell-thumb round" src="{{ \App\Support\ImageStore::url($story->photo_path) }}" alt="" loading="lazy">
                    @else
                      <span class="cell-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($story->name, 0, 1)) }}</span>
                    @endif
                    <div>
                      <div class="cell-title">{{ $story->name }}</div>
                      <div class="cell-sub">{{ $story->trade ?: '—' }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Country">{{ $story->country?->name ?? '—' }}</td>
                <td data-label="Order">{{ $story->sort_order }}</td>
                <td data-label="Visible">
                  @can('stories.update')
                    <button type="button" class="status-toggle" wire:click="togglePublished({{ $story->id }})" role="switch" aria-checked="{{ $story->is_published ? 'true' : 'false' }}" aria-label="Show {{ $story->name }} on website"><span class="switch {{ $story->is_published ? 'on' : '' }}"></span></button>
                  @else
                    {{ $story->is_published ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('stories.update')
                      <a class="icon-action" href="{{ route('admin.stories.edit', $story) }}" aria-label="Edit {{ $story->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('stories.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $story->id }})" aria-label="Delete {{ $story->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $stories->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this story?</h4>
      <p class="modal-sub">The story and photo are removed. To hide it temporarily, use the Visible switch instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
