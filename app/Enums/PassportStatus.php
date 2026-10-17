<?php

declare(strict_types=1);

namespace App\Enums;

enum PassportStatus: string
{
    case Has = 'has';
    case Processing = 'processing';
    case None = 'none';

    public function label(): string
    {
        return match ($this) {
            self::Has => 'Has passport',
            self::Processing => 'Passport in process',
            self::None => 'No passport',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
