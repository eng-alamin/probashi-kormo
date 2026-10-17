<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    private const MIN_PASSWORD_LENGTH = 10;

    public function run(): void
    {
        $name = (string) config('admin.super_admin.name');
        $email = config('admin.super_admin.email');
        $password = config('admin.super_admin.password');

        if (blank($email) || blank($password)) {
            $this->command->error('Super Admin not created: set ADMIN_EMAIL and ADMIN_PASSWORD in .env, then run "php artisan config:clear" and "php artisan db:seed" again.');

            return;
        }

        if (strlen((string) $password) < self::MIN_PASSWORD_LENGTH) {
            $this->command->error('Super Admin not created: ADMIN_PASSWORD must be at least '.self::MIN_PASSWORD_LENGTH.' characters.');

            return;
        }

        // firstOrCreate: running the seeder again never resets an existing password.
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password, 'is_active' => true],
        );

        $user->assignRole(RoleName::SuperAdmin->value);

        $this->command->info("Super Admin ready: {$email}");
    }
}
