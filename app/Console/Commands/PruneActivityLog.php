<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLog extends Command
{
    protected $signature = 'activity:prune {--days= : Delete lines older than this many days (default: ACTIVITY_LOG_DAYS, 180)}';

    protected $description = 'Delete old activity log lines';

    public function handle(): int
    {
        $option = $this->option('days');

        $days = $option === null
            ? (int) config('activity.retention_days')
            : (int) (filter_var($option, FILTER_VALIDATE_INT) ?: 0);

        if ($days < 1) {
            $this->error('The number of days must be a whole number, 1 or more.');

            return self::FAILURE;
        }

        // A plain query delete on purpose: the log is not a watched model.
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} activity log line(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
