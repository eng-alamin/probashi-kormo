<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Builds a URL slug that is unique in the table.
 * A Bangla-only title can produce an empty slug, so a short random fallback is used then.
 */
final class Slug
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function unique(string $modelClass, string $text, ?int $ignoreId = null, string $fallback = 'item'): string
    {
        $base = Str::slug(Str::limit($text, 150, ''));

        if ($base === '') {
            $base = $fallback.'-'.Str::lower(Str::random(6));
        }

        $slug = $base;
        $i = 2;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /**
     * Blank text becomes null so optional columns stay empty instead of holding "".
     */
    public static function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
