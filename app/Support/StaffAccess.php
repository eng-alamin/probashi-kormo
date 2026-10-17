<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\RoleName;
use App\Models\User;

/**
 * Safety rules for managing staff accounts, kept in one place so Users and Roles pages agree.
 *
 * - Only a Super Admin may touch a Super Admin account or hand out the Super Admin role.
 * - The last active Super Admin can never be deleted, deactivated or demoted (no lock-out).
 */
final class StaffAccess
{
    public static function isSuperAdmin(User $user): bool
    {
        return $user->hasRole(RoleName::SuperAdmin->value);
    }

    public static function canManage(User $actor, User $target): bool
    {
        return self::isSuperAdmin($actor) || ! self::isSuperAdmin($target);
    }

    public static function isLastActiveSuperAdmin(User $target): bool
    {
        if (! $target->is_active || ! self::isSuperAdmin($target)) {
            return false;
        }

        return ! User::role(RoleName::SuperAdmin->value)
            ->where('is_active', true)
            ->whereKeyNot($target->getKey())
            ->exists();
    }

    /**
     * Role names the actor may give to someone.
     *
     * @return list<string>
     */
    public static function assignableRoles(User $actor): array
    {
        $roles = self::isSuperAdmin($actor)
            ? RoleName::values()
            : [RoleName::Admin->value, RoleName::Staff->value];

        return array_values($roles);
    }
}
