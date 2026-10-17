<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Public contact details from config/site.php (.env).
 * Every getter returns null when the value is missing, invalid or still a placeholder,
 * so the views can simply hide that piece instead of showing "+880 1XXX-XXXXXX".
 */
final class Site
{
    public static function phone(): ?string
    {
        return self::clean(self::setting('phone'));
    }

    public static function phoneHref(): ?string
    {
        $phone = self::phone();

        if ($phone === null) {
            return null;
        }

        $digits = (string) preg_replace('/[^0-9+]/', '', $phone);

        return $digits !== '' ? 'tel:'.$digits : null;
    }

    public static function email(): ?string
    {
        $email = self::clean(self::setting('email'));

        return ($email !== null && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : null;
    }

    public static function address(): ?string
    {
        return self::clean(self::setting('address'));
    }

    /**
     * Digits only, with country code (for wa.me), e.g. 8801712345678.
     */
    public static function whatsapp(): ?string
    {
        $value = self::clean(self::setting('whatsapp'));

        if ($value === null) {
            return null;
        }

        $digits = (string) preg_replace('/[\s+\-()]/', '', $value);

        return preg_match('/^[1-9][0-9]{7,14}$/', $digits) === 1 ? $digits : null;
    }

    public static function whatsappUrl(): ?string
    {
        $number = self::whatsapp();

        return $number !== null ? 'https://wa.me/'.$number : null;
    }

    /**
     * Settings that must be filled before going live: key => what is wrong.
     *
     * @return array<string, string>
     */
    public static function problems(): array
    {
        $problems = [];

        foreach (['phone' => self::phone(), 'email' => self::email(), 'address' => self::address(), 'whatsapp' => self::whatsapp()] as $key => $value) {
            if ($value === null) {
                $problems[$key] = 'missing, invalid or still a placeholder';
            }
        }

        return $problems;
    }

    /**
     * The saved value (admin > Settings) or, if none, the .env / config/site.php value. Trimmed, or null.
     */
    public static function setting(string $key): ?string
    {
        $value = Settings::get($key) ?? config('site.'.$key);
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public static function hours(): ?string
    {
        return self::clean(self::setting('hours'));
    }

    public static function licenseNo(): ?string
    {
        return self::clean(self::setting('license_no'));
    }

    /**
     * Only Google Maps embed links are accepted.
     */
    public static function mapEmbed(): ?string
    {
        $embed = self::setting('map_embed');

        return ($embed !== null && str_starts_with($embed, 'https://www.google.com/maps/embed')) ? $embed : null;
    }

    /**
     * Home page text and numbers: saved value, else the default in config/site.php.
     *
     * @return array{title: string, highlight: string, text: string, stats: list<array{0: string, 1: string}>}
     */
    public static function home(): array
    {
        $d = (array) config('site.home');
        $pick = static fn (string $key, string $default): string => Settings::get($key) ?? $default;

        $stats = [];
        foreach ([1, 2, 3, 4] as $i) {
            $default = $d['stats'][$i - 1] ?? ['', ''];
            $stats[] = [$pick("stat{$i}_value", (string) $default[0]), $pick("stat{$i}_label", (string) $default[1])];
        }

        return [
            'title' => $pick('home_title', (string) ($d['title'] ?? '')),
            // the red part may be emptied on purpose, so a saved blank value is respected
            'highlight' => Settings::has('home_highlight')
                ? trim((string) (Settings::all()['home_highlight'] ?? ''))
                : (string) ($d['highlight'] ?? ''),
            'text' => $pick('home_text', (string) ($d['text'] ?? '')),
            'stats' => $stats,
        ];
    }

    private static function clean(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || preg_match('/x{3,}/i', $value) === 1 || stripos($value, 'your office address') !== false) {
            return null;
        }

        return $value;
    }
}
