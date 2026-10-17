<div>
  <div class="page-head">
    <div><h2>News &amp; Notice</h2><p>Announcements shown on the website news page</p></div>
    @can('news.create')
      <a href="{{ route('admin.news.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New post</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search title" aria-label="Search news">
      </label>
      <select class="form-select" wire:model.live="state" aria-label="Filter by status">
        <option value="">All posts</option>
        <option value="live">Live</option>
        <option value="scheduled">Scheduled</option>
        <option value="draft">Draft</option>
      </select>
    </div>

    @if ($posts->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-megaphone-fill"></i></div>
        <h6>No posts found</h6>
        <p>Write a post to share news or a notice on the website.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Post</th>
              <th scope="col">Publish date</th>
              <th scope="col">Status</th>
              <th scope="col">Published</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($posts as $post)
              @php($isDraft = ! $post->is_published || $post->published_at === null)
              <tr wire:key="post-{{ $post->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    @if ($post->cover_image_path)
                      <img class="cell-thumb" src="{{ \App\Support\ImageStore::url($post->cover_image_path) }}" alt="" loading="lazy">
                    @else
                      <span class="cell-avatar" aria-hidden="true"><i class="bi bi-megaphone-fill"></i></span>
                    @endif
                    <div>
                      <div class="cell-title">{{ $post->title }}</div>
                      <div class="cell-sub">/news/{{ $post->slug }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Publish date">{{ $post->published_at?->format('d M Y, h:i A') ?? '—' }}</td>
                <td data-label="Status">
                  @if ($isDraft)
                    <span class="status-pill status-suspended">Draft</span>
                  @elseif ($post->published_at->isFuture())
                    <span class="status-pill status-trial">Scheduled</span>
                  @else
                    <span class="status-pill status-active">Live</span>
                  @endif
                </td>
                <td data-label="Published">
                  @can('news.update')
                    <button type="button" class="status-toggle" wire:click="togglePublished({{ $post->id }})" role="switch" aria-checked="{{ $post->is_published ? 'true' : 'false' }}" aria-label="Publish {{ $post->title }}"><span class="switch {{ $post->is_published ? 'on' : '' }}"></span></button>
                  @else
                    {{ $post->is_published ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('news.update')
                      <a class="icon-action" href="{{ route('admin.news.edit', $post) }}" aria-label="Edit {{ $post->title }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('news.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $post->id }})" aria-label="Delete {{ $post->title }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $posts->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this post?</h4>
      <p class="modal-sub">It will be removed from the website, along with its cover image. To hide it temporarily, switch Published off instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
