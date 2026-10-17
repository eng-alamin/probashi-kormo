<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityEvent: string
{
    case Created = 'created';
    case Updated = 'updated';
    case StatusChanged = 'status_changed';
    case Deleted = 'deleted';
    case SignedIn = 'signed_in';
    case SignedOut = 'signed_out';
    case RoleChanged = 'role_changed';
    case PermissionsChanged = 'permissions_changed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Updated => 'Updated',
            self::StatusChanged => 'Status changed',
            self::Deleted => 'Deleted',
            self::SignedIn => 'Signed in',
            self::SignedOut => 'Signed out',
            self::RoleChanged => 'Role changed',
            self::PermissionsChanged => 'Permissions changed',
        };
    }

    /**
     * CSS class of the coloured pill (uses the pills the admin theme already has).
     */
    public function pill(): string
    {
        return match ($this) {
            self::Created, self::SignedIn => 'status-active',
            self::StatusChanged, self::RoleChanged, self::PermissionsChanged => 'status-trial',
            self::Deleted => 'status-past',
            self::Updated, self::SignedOut => 'status-suspended',
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
