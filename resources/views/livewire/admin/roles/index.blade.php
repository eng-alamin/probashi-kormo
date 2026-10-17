<div>
  <div class="page-head">
    <div><h2>Roles &amp; Permissions</h2><p>What each role is allowed to do in the admin panel</p></div>
  </div>

  <div class="glass panel">
    <div class="table-wrap">
      <table class="responsive-table">
        <thead>
          <tr>
            <th scope="col">Role</th>
            <th scope="col">People</th>
            <th scope="col">Permissions</th>
            <th scope="col"><span class="sr-only">Actions</span></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($roles as $role)
            @php
              $isSuper = $role->name === 'Super Admin';
              $pill = match ($role->name) { 'Super Admin' => 'role-super', 'Admin' => 'role-support', default => 'role-billing' };
            @endphp
            <tr wire:key="role-{{ $role->id }}">
              <td class="cell-main-td"><span class="role-pill {{ $pill }}">{{ $role->name }}</span></td>
              <td data-label="People">{{ $role->users_count }}</td>
              <td data-label="Permissions">
                @if ($isSuper)
                  Everything (cannot be changed)
                @else
                  {{ $role->permissions_count }} of {{ $totalPermissions }}
                @endif
              </td>
              <td>
                <div class="row-actions">
                  @if (! $isSuper)
                    @can('roles.update')
                      <a class="icon-action" href="{{ route('admin.roles.edit', $role) }}" aria-label="Edit {{ $role->name }} permissions"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                    @endcan
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <p class="form-hint" style="margin-top:14px;">The three roles are fixed. Change what Admin and Staff can do by editing their permissions. Give a person a role on the Staff Users page.</p>
  </div>
</div>
