<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Enums\ActivityEvent;
use App\Enums\RoleName;
use App\Support\ActivityLogger;
use App\Support\StaffAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $roleId = null;

    public string $roleName = '';

    /**
     * Ticked permissions, like "jobs.view".
     *
     * @var array<int, string>
     */
    public array $selected = [];

    public function mount(Role $role): void
    {
        $this->authorize('roles.update');

        // Super Admin always has everything; there is nothing to edit.
        abort_if($role->name === RoleName::SuperAdmin->value, 403);
        abort_unless(in_array($role->name, RoleName::values(), true), 404);

        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->selected = $role->permissions->pluck('name')->values()->all();
    }

    /**
     * Every permission that exists, "module.action".
     *
     * @return list<string>
     */
    private function allPermissions(): array
    {
        $all = [];

        foreach (config('permissions.modules') as $module => $actions) {
            foreach ($actions as $action) {
                $all[] = "{$module}.{$action}";
            }
        }

        return $all;
    }

    private function actorIsSuperAdmin(): bool
    {
        return StaffAccess::isSuperAdmin(auth()->user());
    }

    /**
     * Modules only a Super Admin may change (Staff Users, Roles & Permissions).
     * An Admin who could tick these for themselves would be able to promote themselves.
     */
    private function isLockedModule(string $module): bool
    {
        return ! $this->actorIsSuperAdmin()
            && in_array($module, config('permissions.admin_excluded_modules', []), true);
    }

    public function resetDefaults(): void
    {
        $this->authorize('roles.update');

        $defaults = $this->roleName === RoleName::Staff->value
            ? config('permissions.staff', [])
            : array_values(array_filter(
                $this->allPermissions(),
                fn (string $name): bool => ! in_array(explode('.', $name)[0], config('permissions.admin_excluded_modules', []), true),
            ));

        $kept = array_values(array_filter(
            $this->selected,
            fn (string $name): bool => $this->isLockedModule(explode('.', $name)[0]),
        ));

        $locked = array_values(array_filter(
            $defaults,
            fn (string $name): bool => ! $this->isLockedModule(explode('.', $name)[0]),
        ));

        $this->selected = array_values(array_unique([...$kept, ...$locked]));
    }

    public function save(): void
    {
        $this->authorize('roles.update');

        $role = Role::findOrFail($this->roleId);

        abort_if($role->name === RoleName::SuperAdmin->value, 403);

        $before = $role->permissions->pluck('name')->all();

        $this->validate([
            'selected' => ['array'],
            'selected.*' => ['string', Rule::in($this->allPermissions())],
        ]);

        $final = [];

        foreach (array_unique($this->selected) as $name) {
            [$module, $action] = explode('.', $name);

            if ($this->isLockedModule($module)) {
                continue;
            }

            $final[] = $name;

            // Adding, editing or deleting is useless without being able to open the list.
            if ($action !== 'view') {
                $final[] = "{$module}.view";
            }
        }

        // Locked modules stay exactly as they are.
        foreach ($role->permissions->pluck('name') as $name) {
            if ($this->isLockedModule(explode('.', $name)[0])) {
                $final[] = $name;
            }
        }

        $after = array_values(array_unique($final));

        $role->syncPermissions($after);

        $this->logPermissionChange($role, $before, $after);

        session()->flash('toast', "{$role->name} permissions saved");

        $this->redirectRoute('admin.roles.index');
    }

    /**
     * Writes one activity-log line that says exactly which permissions were added and removed.
     *
     * @param  list<string>  $before
     * @param  list<string>  $after
     */
    private function logPermissionChange(Role $role, array $before, array $after): void
    {
        $added = array_values(array_diff($after, $before));
        $removed = array_values(array_diff($before, $after));

        if ($added === [] && $removed === []) {
            return;
        }

        $parts = [];

        if ($added !== []) {
            $parts[] = 'added '.implode(', ', $added);
        }

        if ($removed !== []) {
            $parts[] = 'removed '.implode(', ', $removed);
        }

        ActivityLogger::record(
            ActivityEvent::PermissionsChanged,
            "Changed permissions of {$role->name} role: ".implode('; ', $parts),
            'role',
            (int) $role->id,
        );
    }

    public function render(): View
    {
        $modules = [];

        foreach (config('permissions.modules') as $module => $actions) {
            $modules[] = [
                'key' => $module,
                'label' => config("permissions.labels.{$module}", ucfirst($module)),
                'actions' => $actions,
                'locked' => $this->isLockedModule($module),
            ];
        }

        return view('livewire.admin.roles.form', [
            'modules' => $modules,
            'actionLabels' => config('permissions.action_labels'),
            'columns' => ['view', 'create', 'update', 'delete'],
        ])->title('Edit role');
    }
}
