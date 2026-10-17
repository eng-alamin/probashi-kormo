<?php

declare(strict_types=1);

namespace App\Enums;

enum GalleryCategory: string
{
    case Office = 'office';
    case Training = 'training';
    case Departure = 'departure';

    public function label(): string
    {
        return match ($this) {
            self::Office => 'Office',
            self::Training => 'Training',
            self::Departure => 'Departure',
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
