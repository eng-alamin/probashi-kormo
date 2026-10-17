<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\ApplicationStatus;
use App\Models\ActivityLog;
use App\Models\Inquiry;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\TrainingCourse;
use App\Models\User;
use Composer\InstalledVersions;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $canApplications = $user->can('applications.view');
        $canInquiries = $user->can('inquiries.view');
        $canLogs = $user->can('logs.view');

        // Every block is built only when the person may open that section, so a Staff member
        // never sees (or even loads) numbers from modules they have no access to.
        $statusCounts = $canApplications ? $this->applicationStatusCounts() : [];
        $weekBars = $canApplications ? $this->weekBars() : [];
        $inquiryCounts = $canInquiries ? $this->inquiryCounts() : [];

        $recentApplications = $canApplications
            ? JobApplication::query()->with(['jobPost:id,title', 'country:id,name'])->latest()->latest('id')->limit(5)->get()
            : null;

        $recentInquiries = $canInquiries
            ? Inquiry::query()->latest()->latest('id')->limit(5)->get()
            : null;

        $recentActivity = $canLogs
            ? ActivityLog::query()->with('user:id,name')->latestFirst()->limit(6)->get()
            : null;

        $cards = $this->cards($user, $statusCounts, $weekBars, $inquiryCounts);

        return view('livewire.admin.dashboard', [
            'cards' => $cards,
            'statusRows' => $canApplications ? $this->statusRows($statusCounts) : null,
            'weekBars' => $canApplications ? $weekBars : null,
            'recentApplications' => $recentApplications,
            'recentInquiries' => $recentInquiries,
            'recentActivity' => $recentActivity,
            'isEmpty' => $cards === [] && ! $canApplications && ! $canInquiries && ! $canLogs,
            'environment' => $this->environment(),
        ]);
    }

    /**
     * @return array<string, int> status value => number of applications
     */
    private function applicationStatusCounts(): array
    {
        return JobApplication::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(static fn ($total): int => (int) $total)
            ->all();
    }

    /**
     * @return array{total: int, new: int}
     */
    private function inquiryCounts(): array
    {
        return [
            'total' => Inquiry::count(),
            'new' => Inquiry::where('status', 'new')->count(),
        ];
    }

    /**
     * Applications received on each of the last 7 days (today is the last bar).
     *
     * @return list<array{label: string, count: int, height: int, title: string}>
     */
    private function weekBars(): array
    {
        $start = now()->startOfDay()->subDays(6);

        $perDay = JobApplication::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $max = max(1, (int) $perDay->max());
        $bars = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $count = (int) ($perDay[$date->toDateString()] ?? 0);

            $bars[] = [
                'label' => $date->format('D'),
                'count' => $count,
                'height' => $count === 0 ? 4 : max(12, (int) round($count / $max * 100)),
                'title' => $date->format('d M').': '.$count,
            ];
        }

        return $bars;
    }

    /**
     * @param  array<string, int>  $statusCounts
     * @return list<array{key: string, label: string, count: int, percent: int}>
     */
    private function statusRows(array $statusCounts): array
    {
        $total = array_sum($statusCounts);
        $rows = [];

        foreach (ApplicationStatus::cases() as $status) {
            $count = $statusCounts[$status->value] ?? 0;

            $rows[] = [
                'key' => $status->value,
                'label' => $status->label(),
                'count' => $count,
                'percent' => $total > 0 ? (int) round($count / $total * 100) : 0,
            ];
        }

        return $rows;
    }

    /**
     * The summary cards at the top, only for sections the person can open.
     *
     * @param  array<string, int>  $statusCounts
     * @param  list<array{label: string, count: int, height: int, title: string}>  $weekBars
     * @param  array{total?: int, new?: int}  $inquiryCounts
     * @return list<array{icon: string, style: string, label: string, value: int, note: string, url: string}>
     */
    private function cards(User $user, array $statusCounts, array $weekBars, array $inquiryCounts): array
    {
        $cards = [];

        if ($user->can('applications.view')) {
            $thisWeek = array_sum(array_column($weekBars, 'count'));

            $cards[] = [
                'icon' => 'bi-person-lines-fill',
                'style' => 'background:var(--cobalt-soft); color:#7EA1F5;',
                'label' => 'Applications',
                'value' => array_sum($statusCounts),
                'note' => ($statusCounts[ApplicationStatus::New->value] ?? 0).' new · '.$thisWeek.' this week',
                'url' => route('admin.applications.index'),
            ];
        }

        if ($user->can('jobs.view')) {
            $open = JobPost::open()->count();

            $cards[] = [
                'icon' => 'bi-briefcase-fill',
                'style' => 'background:var(--ok-soft); color:#4ADE80;',
                'label' => 'Open Jobs',
                'value' => $open,
                'note' => 'of '.JobPost::count().' jobs',
                'url' => route('admin.jobs.index'),
            ];
        }

        if ($user->can('trainings.view')) {
            $cards[] = [
                'icon' => 'bi-mortarboard-fill',
                'style' => 'background:rgba(255,201,60,.15); color:var(--citrus);',
                'label' => 'Training Courses',
                'value' => TrainingCourse::active()->count(),
                'note' => 'of '.TrainingCourse::count().' courses visible',
                'url' => route('admin.trainings.index'),
            ];
        }

        if ($user->can('inquiries.view')) {
            $cards[] = [
                'icon' => 'bi-inbox-fill',
                'style' => 'background:var(--danger-soft); color:var(--danger);',
                'label' => 'New Messages',
                'value' => $inquiryCounts['new'] ?? 0,
                'note' => 'of '.($inquiryCounts['total'] ?? 0).' messages',
                'url' => route('admin.inquiries.index'),
            ];
        }

        return $cards;
    }

    /**
     * Version info is shown only on a local machine, never on a live server.
     *
     * @return array<string, string>
     */
    private function environment(): array
    {
        if (! app()->isLocal()) {
            return [];
        }

        return [
            'Laravel' => app()->version(),
            'PHP' => PHP_VERSION,
            'Livewire' => InstalledVersions::getPrettyVersion('livewire/livewire') ?? 'unknown',
            'Database' => (string) config('database.default'),
            'Timezone' => (string) config('app.timezone'),
        ];
    }
}
