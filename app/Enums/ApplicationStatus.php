<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Shortlisted = 'shortlisted';
    case Rejected = 'rejected';
    case Placed = 'placed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Shortlisted => 'Shortlisted',
            self::Rejected => 'Rejected',
            self::Placed => 'Placed',
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
