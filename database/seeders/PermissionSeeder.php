<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $all = [];
        foreach (config('permissions.modules') as $module => $actions) {
            foreach ($actions as $action) {
                $name = "{$module}.{$action}";
                Permission::findOrCreate($name, 'web');
                $all[] = $name;
            }
        }

        $excluded = config('permissions.admin_excluded_modules', []);
        $adminPermissions = array_values(array_filter(
            $all,
            static fn (string $name): bool => ! in_array(Str::before($name, '.'), $excluded, true),
        ));

        // givePermissionTo is additive, so running the seeder again never removes
        // a permission that was changed later from the Roles page.
        Role::findByName(RoleName::Admin->value, 'web')->givePermissionTo($adminPermissions);
        Role::findByName(RoleName::Staff->value, 'web')->givePermissionTo(config('permissions.staff', []));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
