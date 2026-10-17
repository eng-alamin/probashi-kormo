<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Small helpers for Bangladeshi phone numbers saved by the public forms.
 */
final class Phone
{
    /**
     * Number in international digits for wa.me links, e.g. 01712345678 -> 8801712345678.
     */
    public static function international(string $phone): string
    {
        $digits = (string) preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '880')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '88'.$digits;
        }

        return $digits;
    }

    public static function whatsappUrl(string $phone): string
    {
        return 'https://wa.me/'.self::international($phone);
    }
}
