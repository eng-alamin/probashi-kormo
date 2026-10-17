<?php

declare(strict_types=1);

namespace App\Support;

/**
 * List columns (job_sectors, learn_points ...) are plain text, one item per line.
 * This turns that text into a clean array for the views.
 */
final class Lines
{
    /**
     * @return list<string>
     */
    public static function split(?string $text): array
    {
        if ($text === null || trim($text) === '') {
            return [];
        }

        $lines = preg_split('/\R/u', $text) ?: [];

        return array_values(array_filter(
            array_map('trim', $lines),
            static fn (string $line): bool => $line !== '',
        ));
    }
}
