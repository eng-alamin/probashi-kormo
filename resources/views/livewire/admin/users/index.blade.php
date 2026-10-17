<div>
  <div class="page-head">
    <div><h2>Staff Users</h2><p>People who can sign in to this admin panel</p></div>
    @can('users.create')
      <a href="{{ route('admin.users.create') }}" class="btn btn-cobalt btn-sm"><i class="bi bi-plus-lg" aria-hidden="true"></i> New staff user</a>
    @endcan
  </div>

  <div class="glass panel">
    <div class="list-toolbar">
      <label class="search-box">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search name or email" aria-label="Search staff">
      </label>
      <select class="form-select" wire:model.live="role" aria-label="Filter by role">
        <option value="">All roles</option>
        @foreach ($roleNames as $r)
          <option value="{{ $r }}">{{ $r }}</option>
        @endforeach
      </select>
    </div>

    @if ($users->isEmpty())
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
        <h6>No staff found</h6>
        <p>Try a different search.</p>
      </div>
    @else
      <div class="table-wrap">
        <table class="responsive-table">
          <thead>
            <tr>
              <th scope="col">Person</th>
              <th scope="col">Role</th>
              <th scope="col">Active</th>
              <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $u)
              @php
                $roleName = (string) $u->roles->first()?->name;
                $isMe = $u->is(auth()->user());
                $manageable = \App\Support\StaffAccess::canManage(auth()->user(), $u);
                $pill = match ($roleName) { 'Super Admin' => 'role-super', 'Admin' => 'role-support', default => 'role-billing' };
              @endphp
              <tr wire:key="user-{{ $u->id }}">
                <td class="cell-main-td">
                  <div class="cell-main">
                    <span class="cell-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                    <div>
                      <div class="cell-title">{{ $u->name }}@if ($isMe) <span class="plan-tag">You</span>@endif</div>
                      <div class="cell-sub">{{ $u->email }}</div>
                    </div>
                  </div>
                </td>
                <td data-label="Role"><span class="role-pill {{ $pill }}">{{ $roleName ?: 'No role' }}</span></td>
                <td data-label="Active">
                  @if ($manageable && ! $isMe)
                    @can('users.update')
                      <button type="button" class="status-toggle" wire:click="toggleActive({{ $u->id }})" role="switch" aria-checked="{{ $u->is_active ? 'true' : 'false' }}" aria-label="Account active for {{ $u->name }}"><span class="switch {{ $u->is_active ? 'on' : '' }}"></span></button>
                    @else
                      {{ $u->is_active ? 'Yes' : 'No' }}
                    @endcan
                  @else
                    {{ $u->is_active ? 'Yes' : 'No' }}
                  @endif
                </td>
                <td>
                  <div class="row-actions">
                    @if ($manageable)
                      @can('users.update')
                        <a class="icon-action" href="{{ route('admin.users.edit', $u) }}" aria-label="Edit {{ $u->name }}"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                      @endcan
                      @if (! $isMe)
                        @can('users.delete')
                          <button type="button" class="icon-action danger" wire:click="confirmDelete({{ $u->id }})" aria-label="Delete {{ $u->name }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                        @endcan
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $users->links('pagination.admin') }}
    @endif
  </div>

  <div class="modal-overlay {{ $deletingId ? 'open' : '' }}" role="dialog" aria-modal="true" aria-labelledby="delTitle" @if (! $deletingId) aria-hidden="true" @endif>
    <div class="modal-box">
      <div class="confirm-icon" aria-hidden="true"><i class="bi bi-trash"></i></div>
      <h4 id="delTitle">Delete this account?</h4>
      <p class="modal-sub">They can no longer sign in. To stop access but keep the record, switch Active off instead.</p>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>
        <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
      </div>
    </div>
  </div>
</div>
