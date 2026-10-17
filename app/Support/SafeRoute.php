<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Builds a URL for a named route, or "#" while that page is not built yet.
 * This lets menus and buttons exist before every page is ready.
 */
final class SafeRoute
{
    public static function url(string $name): string
    {
        return Route::has($name) ? route($name) : '#';
    }

    public static function exists(string $name): bool
    {
        return Route::has($name);
    }
}
