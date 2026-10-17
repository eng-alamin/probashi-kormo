<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Enums\RoleName;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts::admin')]
#[Title('Roles & Permissions')]
class Index extends Component
{
    public function render(): View
    {
        $this->authorize('roles.view');

        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->whereIn('name', RoleName::values())
            ->get()
            ->sortBy(fn (Role $role) => array_search($role->name, RoleName::values(), true))
            ->values();

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
            'totalPermissions' => collect(config('permissions.modules'))->flatten()->count(),
        ]);
    }
}
