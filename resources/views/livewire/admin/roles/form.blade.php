<div>
  <div class="page-head">
    <div><h2>{{ $roleName }} permissions</h2><p>Tick what this role may do. Changes apply to everyone with this role.</p></div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back</a>
  </div>

  <form wire:submit="save" novalidate class="glass panel">
    <div class="table-wrap">
      <table class="perm-table">
        <thead>
          <tr>
            <th scope="col">Section</th>
            @foreach ($columns as $col)
              <th scope="col" class="perm-cell">{{ $actionLabels[$col] }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($modules as $m)
            <tr wire:key="perm-{{ $m['key'] }}">
              <th scope="row" class="perm-name">
                {{ $m['label'] }}
                @if ($m['locked'])<span class="cell-sub" style="display:block;">Only a Super Admin can change this</span>@endif
              </th>
              @foreach ($columns as $col)
                <td class="perm-cell">
                  @if (in_array($col, $m['actions'], true))
                    <input type="checkbox" class="form-check-input" value="{{ $m['key'].'.'.$col }}" wire:model="selected" @disabled($m['locked']) aria-label="{{ $actionLabels[$col] }} {{ $m['label'] }}">
                  @else
                    <span class="cell-sub" aria-hidden="true">—</span>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="form-hint" style="margin-top:12px;">Add, Edit and Delete need View, so View is switched on for you automatically when you save.</div>
    @error('selected.*')<div class="field-error show" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i>{{ $message }}</div>@enderror

    <div class="form-actions">
      <button type="submit" class="btn btn-cobalt" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">Save permissions</span>
        <span wire:loading wire:target="save">Saving…</span>
      </button>
      <button type="button" class="btn btn-ghost" wire:click="resetDefaults">Reset to default</button>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
