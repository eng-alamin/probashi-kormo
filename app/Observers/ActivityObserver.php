<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\Inquiry;
use App\Support\ActivityLogger;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes a log line when a signed-in person creates, changes or deletes a watched model
 * (the list lives in config/activity.php).
 *
 * - Seeders, tinker and the public website (nobody signed in) are never logged.
 * - Only column NAMES are logged, never values.
 */
final class ActivityObserver
{
    /** Columns that change on their own and say nothing about what a person did. */
    private const NOISE = ['created_at', 'updated_at', 'remember_token'];

    public function created(Model $model): void
    {
        $target = $this->target($model);

        if ($target === null) {
            return;
        }

        ActivityLogger::record(
            ActivityEvent::Created,
            "Created {$target['label']} \"{$target['name']}\"",
            $target['type'],
            (int) $model->getKey(),
        );
    }

    public function updated(Model $model): void
    {
        $target = $this->target($model);

        if ($target === null) {
            return;
        }

        $changed = array_values(array_diff(
            array_keys($model->getChanges()),
            self::NOISE,
            $target['ignore'],
        ));

        if ($changed === []) {
            return;
        }

        // Opening a new inquiry marks it as read by itself; that is not a decision by the person.
        if ($model instanceof Inquiry && $this->isAutomaticRead($model, $changed)) {
            return;
        }

        if (in_array('status', $changed, true)) {
            $text = sprintf(
                'Changed status of %s "%s" from %s to %s',
                $target['label'],
                $target['name'],
                $this->statusLabel($model->getOriginal('status')),
                $this->statusLabel($model->getAttribute('status')),
            );

            $others = array_values(array_diff($changed, ['status']));

            if ($others !== []) {
                $text .= '; also changed: '.$this->fields($others);
            }

            ActivityLogger::record(ActivityEvent::StatusChanged, $text, $target['type'], (int) $model->getKey());

            return;
        }

        ActivityLogger::record(
            ActivityEvent::Updated,
            "Updated {$target['label']} \"{$target['name']}\" (changed: {$this->fields($changed)})",
            $target['type'],
            (int) $model->getKey(),
        );
    }

    public function deleted(Model $model): void
    {
        $target = $this->target($model);

        if ($target === null) {
            return;
        }

        ActivityLogger::record(
            ActivityEvent::Deleted,
            "Deleted {$target['label']} \"{$target['name']}\"",
            $target['type'],
            (int) $model->getKey(),
        );
    }

    /**
     * @return array{type: string, label: string, name: string, ignore: list<string>}|null
     */
    private function target(Model $model): ?array
    {
        if (! auth()->check()) {
            return null;
        }

        $meta = (config('activity.models', []))[$model::class] ?? null;

        if ($meta === null) {
            return null;
        }

        $name = trim((string) $model->getAttribute($meta['name']));

        return [
            'type' => $meta['type'],
            'label' => $meta['label'],
            'name' => $name !== '' ? $name : '#'.$model->getKey(),
            'ignore' => $meta['ignore'] ?? [],
        ];
    }

    /**
     * @param  list<string>  $changed
     */
    private function isAutomaticRead(Inquiry $inquiry, array $changed): bool
    {
        return $changed === ['status']
            && $this->scalar($inquiry->getOriginal('status')) === 'new'
            && $this->scalar($inquiry->getAttribute('status')) === 'read';
    }

    private function scalar(mixed $value): string
    {
        return $value instanceof BackedEnum ? (string) $value->value : (string) $value;
    }

    private function statusLabel(mixed $value): string
    {
        if ($value instanceof BackedEnum) {
            return method_exists($value, 'label') ? (string) $value->label() : (string) $value->value;
        }

        return ucfirst((string) $value);
    }

    /**
     * Turns column names into readable words: "is_active" -> "active", "country_id" -> "country".
     *
     * @param  list<string>  $columns
     */
    private function fields(array $columns): string
    {
        return implode(', ', array_map(static function (string $column): string {
            $column = preg_replace('/_id$/', '', $column) ?? $column;
            $column = preg_replace('/^is_/', '', $column) ?? $column;

            return str_replace('_', ' ', $column);
        }, $columns));
    }
}
