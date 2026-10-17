<?php

namespace App\Providers;

use App\Enums\RoleName;
use App\Http\Middleware\EnsureAdminAccess;
use App\Models\User;
use App\Observers\ActivityObserver;
use App\Support\ActivityLogger;
use App\Support\Settings;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The settings table is read once per request; start every request/test clean.
        Settings::flush();

        // Super Admin passes every permission check.
        Gate::before(static fn (User $user): ?bool => $user->hasRole(RoleName::SuperAdmin->value) ? true : null);

        // Re-check admin access on every Livewire update request, not only on page load.
        Livewire::addPersistentMiddleware([
            EnsureAdminAccess::class,
        ]);

        // Activity log: every model listed in config/activity.php is watched.
        // Only changes made by a signed-in person are written (see ActivityObserver).
        foreach (array_keys(config('activity.models', [])) as $modelClass) {
            $modelClass::observe(ActivityObserver::class);
        }

        Event::listen(Login::class, [ActivityLogger::class, 'signedIn']);
        Event::listen(Logout::class, [ActivityLogger::class, 'signedOut']);
    }
}
