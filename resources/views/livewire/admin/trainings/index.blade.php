<div>
  <div class="page-head">
    <div><h2>Training</h2><p>Courses shown on the website</p></div>
    @can('trainings.create')
      <a href="{{ route('admin.trainings.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New course</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search course" aria-label="Search courses">
      </label>
    </div>

    @if ($courses->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-mortarboard-fill"></i></div>
        <h6>No courses found</h6>
        <p>Add a course to show it on the website.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Course</th>
              <th scope="col">Duration</th>
              <th scope="col">Fee</th>
              <th scope="col">Order</th>
              <th scope="col">Visible</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($courses as $course)
              <tr wire:key="course-{{ $course->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" style="font-size:1.1rem;" aria-hidden="true">{{ $course->icon ?: mb_strtoupper(mb_substr($course->title, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $course->title }}</div>
                      <div class="cell-sub">/training/{{ $course->slug }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Duration">{{ $course->duration_weeks ? $course->duration_weeks.' weeks' : '—' }}</td>
                <td data-label="Fee">{{ $course->fee !== null ? '৳ '.number_format($course->fee) : '—' }}</td>
                <td data-label="Order">{{ $course->sort_order }}</td>
                <td data-label="Visible">
                  @can('trainings.update')
                    <button type="button" class="status-toggle" wire:click="toggleActive({{ $course->id }})" role="switch" aria-checked="{{ $course->is_active ? 'true' : 'false' }}" aria-label="Show {{ $course->title }} on website"><span class="switch {{ $course->is_active ? 'on' : '' }}"></span></button>
                  @else
                    {{ $course->is_active ? 'Yes' : 'No' }}
                  @endcan
                </td>
                <td>
                  <div class="row-actions">
                    @can('trainings.update')
                      <a class="icon-action" href="{{ route('admin.trainings.edit', $course) }}" aria-label="Edit {{ $course->title }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                    @can('trainings.delete')
                      <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $course->id }})" aria-label="Delete {{ $course->title }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
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
      <h4 id="delTitle">Delete this course?</h4>
      <p class="modal-sub">It will be removed from the website. To hide it temporarily, use the Visible switch instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
