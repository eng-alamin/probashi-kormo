<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Str;
use Throwable;

/**
 * The one place that writes to the activity log.
 *
 * Logging must never get in the way of real work, so any failure here is reported
 * (to storage/logs) and swallowed: the admin's save or delete still goes through.
 */
final class ActivityLogger
{
    public static function record(
        ActivityEvent $event,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?User $actor = null,
    ): void {
        try {
            $current = auth()->user();
            $actor ??= $current instanceof User ? $current : null;

            ActivityLog::create([
                'user_id' => $actor?->getKey(),
                'actor_name' => $actor?->name,
                'event' => $event->value,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'description' => Str::limit($description, 250),
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Listener for Laravel's Login event. Only people who can use the admin panel are logged.
     */
    public static function signedIn(Login $event): void
    {
        if ($event->user instanceof User && $event->user->canAccessAdmin()) {
            self::record(ActivityEvent::SignedIn, 'Signed in', 'user', (int) $event->user->getKey(), $event->user);
        }
    }

    /**
     * Listener for Laravel's Logout event.
     */
    public static function signedOut(Logout $event): void
    {
        if ($event->user instanceof User && $event->user->canAccessAdmin()) {
            self::record(ActivityEvent::SignedOut, 'Signed out', 'user', (int) $event->user->getKey(), $event->user);
        }
    }
}
