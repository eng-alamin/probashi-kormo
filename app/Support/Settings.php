<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\SiteSetting;
use Throwable;

/**
 * Site settings kept in the database (admin > Settings).
 * A missing or blank value means "not set here": the caller falls back to .env / config/site.php.
 * The table is read once per request. If it does not exist yet (before "php artisan migrate")
 * everything simply falls back, so the public site never breaks.
 */
final class Settings
{
    /** @var array<string, string|null>|null */
    private static ?array $all = null;

    /**
     * @return array<string, string|null>
     */
    public static function all(): array
    {
        if (self::$all === null) {
            try {
                self::$all = SiteSetting::query()->pluck('value', 'key')->all();
            } catch (Throwable) {
                self::$all = [];
            }
        }

        return self::$all;
    }

    public static function get(string $key): ?string
    {
        $value = self::all()[$key] ?? null;

        if ($value === null || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    /**
     * True when a row exists, even if its value is blank (used where "blank on purpose" is allowed).
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, self::all());
    }

    /**
     * Saves many values at once. A blank value deletes the row (back to the .env / default value),
     * except for keys listed in $keepBlank, where an empty value is saved as "intentionally empty".
     *
     * @param  array<string, string|null>  $values
     * @param  list<string>  $keepBlank
     */
    public static function set(array $values, array $keepBlank = []): void
    {
        foreach ($values as $key => $value) {
            $value = $value === null ? '' : trim($value);

            if ($value === '' && ! in_array($key, $keepBlank, true)) {
                SiteSetting::query()->where('key', $key)->delete();
            } else {
                SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        self::flush();
    }

    public static function flush(): void
    {
        self::$all = null;
    }
}
