<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
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

    public function test_login_screen_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign in');
    }

    public function test_admin_can_log_in(): void
    {
        $user = $this->makeUser(RoleName::Admin);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->makeUser(RoleName::Admin);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = $this->makeUser(RoleName::Admin, active: false);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    }

    public function test_user_without_a_role_cannot_log_in(): void
    {
        $user = $this->makeUser(null);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    }

    public function test_too_many_failed_attempts_are_blocked(): void
    {
        $user = $this->makeUser(RoleName::Admin);

        $component = Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        foreach (range(1, 5) as $ignored) {
            $component->call('login');
        }

        $component->call('login')->assertSee('Too many login attempts');

        $this->assertGuest();
    }
}
