<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (RoleName::values() as $name) {
            Role::findOrCreate($name, 'web');
        }
    }

    private function makeUser(?RoleName $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);

        if ($role !== null) {
            $user->assignRole($role->value);
        }

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_every_staff_role_can_open_the_dashboard(): void
    {
        foreach (RoleName::cases() as $role) {
            $this->actingAs($this->makeUser($role))
                ->get('/admin')
                ->assertOk()
                ->assertSee('Dashboard');
        }
    }

    public function test_user_without_a_role_is_logged_out(): void
    {
        $this->actingAs($this->makeUser(null))
            ->get('/admin')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $this->actingAs($this->makeUser(RoleName::Admin, active: false))
            ->get('/admin')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs($this->makeUser(RoleName::Staff))
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
