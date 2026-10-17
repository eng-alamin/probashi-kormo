<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Users;
use App\Models\User;
use App\Support\StaffAccess;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersRolesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(PermissionSeeder::class);
    }

    private function userWithRole(RoleName $role, array $attrs = []): User
    {
        $user = User::factory()->create(array_merge(['is_active' => true], $attrs));
        $user->assignRole($role->value);

        return $user;
    }

    private function superAdmin(): User
    {
        $user = $this->userWithRole(RoleName::SuperAdmin);
        $this->actingAs($user);

        return $user;
    }

    /** Gives the Admin role the users/roles permissions, the way a Super Admin could. */
    private function adminWithUserPermissions(): User
    {
        Role::findByName(RoleName::Admin->value)->givePermissionTo(['users.view', 'users.create', 'users.update', 'users.delete', 'roles.view', 'roles.update']);
        $user = $this->userWithRole(RoleName::Admin);
        $this->actingAs($user);

        return $user;
    }

    // ---------- access ----------

    public function test_admin_and_staff_cannot_open_users_or_roles_by_default(): void
    {
        foreach ([RoleName::Admin, RoleName::Staff] as $role) {
            $this->actingAs($this->userWithRole($role));

            $this->get(route('admin.users.index'))->assertForbidden();
            $this->get(route('admin.users.create'))->assertForbidden();
            $this->get(route('admin.roles.index'))->assertForbidden();
        }
    }

    public function test_super_admin_can_open_the_pages(): void
    {
        $me = $this->superAdmin();
        $this->userWithRole(RoleName::Staff, ['name' => 'Rina Staff']);

        $this->get(route('admin.users.index'))->assertOk()->assertSee('Rina Staff')->assertSee($me->email);
        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.roles.index'))->assertOk()->assertSee('Staff')->assertSee('Admin');
    }

    // ---------- users ----------

    public function test_super_admin_can_create_a_staff_user_who_can_sign_in(): void
    {
        $this->superAdmin();

        Livewire::test(Users\Form::class)
            ->set('name', 'Sabbir Hossain')
            ->set('email', ' Sabbir@Example.com ')
            ->set('password', 'Strong1234pw')
            ->set('password_confirmation', 'Strong1234pw')
            ->set('role', 'Staff')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.users.index'));

        $user = User::firstWhere('email', 'sabbir@example.com');
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Staff'));
        $this->assertTrue($user->is_active);
        $this->assertNotSame('Strong1234pw', $user->password);
        $this->assertTrue($user->canAccessAdmin());
    }

    public function test_user_validation(): void
    {
        $this->superAdmin();
        $existing = $this->userWithRole(RoleName::Staff);

        Livewire::test(Users\Form::class)
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'password' => 'required']);

        Livewire::test(Users\Form::class)
            ->set('name', 'Someone')->set('email', $existing->email)
            ->set('password', 'Strong1234pw')->set('password_confirmation', 'Strong1234pw')
            ->call('save')
            ->assertHasErrors('email');

        Livewire::test(Users\Form::class)
            ->set('name', 'Someone')->set('email', 'new@example.com')
            ->set('password', 'short1')->set('password_confirmation', 'short1')
            ->call('save')
            ->assertHasErrors('password');

        Livewire::test(Users\Form::class)
            ->set('name', 'Someone')->set('email', 'new@example.com')
            ->set('password', 'Strong1234pw')->set('password_confirmation', 'Different1234')
            ->call('save')
            ->assertHasErrors('password');

        Livewire::test(Users\Form::class)
            ->set('name', 'Someone')->set('email', 'new@example.com')
            ->set('password', 'Strong1234pw')->set('password_confirmation', 'Strong1234pw')
            ->set('role', 'Hacker')
            ->call('save')
            ->assertHasErrors('role');
    }

    public function test_editing_without_a_password_keeps_the_old_one_and_can_change_role(): void
    {
        $this->superAdmin();
        $target = $this->userWithRole(RoleName::Staff);
        $oldHash = $target->password;

        Livewire::test(Users\Form::class, ['user' => $target])
            ->set('name', 'Renamed Person')
            ->set('role', 'Admin')
            ->call('save')
            ->assertHasNoErrors();

        $target->refresh();
        $this->assertSame('Renamed Person', $target->name);
        $this->assertSame($oldHash, $target->password);
        $this->assertTrue($target->hasRole('Admin'));
        $this->assertFalse($target->hasRole('Staff'));
    }

    public function test_deactivated_user_cannot_use_the_admin(): void
    {
        $this->superAdmin();
        $target = $this->userWithRole(RoleName::Staff);

        Livewire::test(Users\Index::class)->call('toggleActive', $target->id);

        $this->assertFalse($target->fresh()->is_active);

        Livewire::test(Users\Index::class)->call('toggleActive', $target->id);

        $this->assertTrue($target->fresh()->is_active);
    }

    public function test_you_cannot_deactivate_or_delete_yourself(): void
    {
        $me = $this->superAdmin();
        $this->userWithRole(RoleName::SuperAdmin);

        Livewire::test(Users\Index::class)
            ->call('toggleActive', $me->id)
            ->call('confirmDelete', $me->id)
            ->call('delete');

        $me->refresh();
        $this->assertTrue($me->is_active);
        $this->assertModelExists($me);
    }

    public function test_last_active_super_admin_rule(): void
    {
        $first = $this->userWithRole(RoleName::SuperAdmin);
        $staff = $this->userWithRole(RoleName::Staff);

        $this->assertTrue(StaffAccess::isLastActiveSuperAdmin($first));
        $this->assertFalse(StaffAccess::isLastActiveSuperAdmin($staff));

        $second = $this->userWithRole(RoleName::SuperAdmin);
        $this->assertFalse(StaffAccess::isLastActiveSuperAdmin($first));
        $this->assertFalse(StaffAccess::isLastActiveSuperAdmin($second));

        $second->update(['is_active' => false]);
        $this->assertTrue(StaffAccess::isLastActiveSuperAdmin($first->fresh()));
    }

    public function test_your_own_role_and_active_switch_are_locked(): void
    {
        $me = $this->superAdmin();

        Livewire::test(Users\Form::class, ['user' => $me])
            ->set('name', 'My New Name')
            ->set('role', 'Staff')
            ->set('is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        $me->refresh();
        $this->assertSame('My New Name', $me->name);
        $this->assertTrue($me->hasRole('Super Admin'));
        $this->assertTrue($me->is_active);
    }

    public function test_admin_with_user_permissions_cannot_touch_super_admins_or_hand_out_the_role(): void
    {
        $superAdmin = $this->userWithRole(RoleName::SuperAdmin);
        $this->adminWithUserPermissions();

        $this->get(route('admin.users.edit', $superAdmin))->assertForbidden();

        Livewire::test(Users\Index::class)
            ->call('toggleActive', $superAdmin->id)
            ->assertForbidden();

        Livewire::test(Users\Form::class)
            ->set('name', 'Sneaky')->set('email', 'sneaky@example.com')
            ->set('password', 'Strong1234pw')->set('password_confirmation', 'Strong1234pw')
            ->set('role', 'Super Admin')
            ->call('save')
            ->assertHasErrors('role');

        $this->assertNull(User::firstWhere('email', 'sneaky@example.com'));
        $this->assertTrue($superAdmin->fresh()->is_active);
    }

    public function test_deleting_a_staff_user(): void
    {
        $this->superAdmin();
        $target = $this->userWithRole(RoleName::Staff);

        Livewire::test(Users\Index::class)
            ->call('confirmDelete', $target->id)
            ->call('delete');

        $this->assertModelMissing($target);
    }

    // ---------- roles ----------

    public function test_super_admin_role_cannot_be_edited(): void
    {
        $this->superAdmin();
        $role = Role::findByName(RoleName::SuperAdmin->value);

        $this->get(route('admin.roles.edit', $role))->assertForbidden();
    }

    public function test_super_admin_can_change_staff_permissions(): void
    {
        $this->superAdmin();
        $role = Role::findByName(RoleName::Staff->value);

        $this->get(route('admin.roles.edit', $role))->assertOk()->assertSee('Staff permissions');

        Livewire::test(Roles\Form::class, ['role' => $role])
            ->set('selected', ['applications.view', 'jobs.update', 'gallery.view'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.roles.index'));

        $role->refresh();
        $names = $role->permissions->pluck('name')->all();

        // jobs.update pulls in jobs.view automatically; everything not ticked is removed.
        $this->assertEqualsCanonicalizing(['applications.view', 'jobs.update', 'jobs.view', 'gallery.view'], $names);
    }

    public function test_changed_permissions_take_effect_for_users(): void
    {
        $staff = $this->userWithRole(RoleName::Staff);
        $this->actingAs($staff)->get(route('admin.gallery.index'))->assertForbidden();

        $this->actingAs($this->userWithRole(RoleName::SuperAdmin));
        Livewire::test(Roles\Form::class, ['role' => Role::findByName(RoleName::Staff->value)])
            ->set('selected', ['gallery.view'])
            ->call('save');

        $this->actingAs($staff->fresh())->get(route('admin.gallery.index'))->assertOk();
        $this->actingAs($staff->fresh())->get(route('admin.applications.index'))->assertForbidden();
    }

    public function test_unknown_permission_names_are_rejected(): void
    {
        $this->superAdmin();

        Livewire::test(Roles\Form::class, ['role' => Role::findByName(RoleName::Staff->value)])
            ->set('selected', ['everything.delete'])
            ->call('save')
            ->assertHasErrors('selected.0');
    }

    public function test_admin_cannot_give_a_role_users_or_roles_permissions(): void
    {
        $this->adminWithUserPermissions();
        $staff = Role::findByName(RoleName::Staff->value);

        Livewire::test(Roles\Form::class, ['role' => $staff])
            ->set('selected', ['applications.view', 'users.view', 'users.update', 'roles.update'])
            ->call('save')
            ->assertHasNoErrors();

        $names = $staff->fresh()->permissions->pluck('name')->all();
        $this->assertContains('applications.view', $names);
        $this->assertNotContains('users.view', $names);
        $this->assertNotContains('users.update', $names);
        $this->assertNotContains('roles.update', $names);
    }

    public function test_admin_keeps_existing_locked_permissions_when_saving_own_role(): void
    {
        $this->adminWithUserPermissions();
        $adminRole = Role::findByName(RoleName::Admin->value);

        Livewire::test(Roles\Form::class, ['role' => $adminRole])
            ->set('selected', ['jobs.view'])
            ->call('save')
            ->assertHasNoErrors();

        $names = $adminRole->fresh()->permissions->pluck('name')->all();
        $this->assertContains('users.update', $names);
        $this->assertContains('roles.update', $names);
        $this->assertContains('jobs.view', $names);
        $this->assertNotContains('applications.view', $names);
    }

    public function test_reset_to_default_restores_staff_permissions_in_the_form_only(): void
    {
        $this->superAdmin();
        $role = Role::findByName(RoleName::Staff->value);
        $role->syncPermissions(['gallery.view']);

        Livewire::test(Roles\Form::class, ['role' => $role->fresh()])
            ->call('resetDefaults')
            ->assertSet('selected', config('permissions.staff'));

        $this->assertSame(['gallery.view'], $role->fresh()->permissions->pluck('name')->all());
    }
}
