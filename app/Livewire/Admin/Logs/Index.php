<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Logs;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Activity Log')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $kind = '';

    #[Url(except: '')]
    public string $staff = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKind(): void
    {
        $this->resetPage();
    }

    public function updatedStaff(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $this->authorize('logs.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $logs = ActivityLog::query()
            ->with('user:id,name')
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('description', 'like', $like)
                ->orWhere('actor_name', 'like', $like)))
            ->when(in_array($this->kind, ActivityEvent::values(), true), fn ($q) => $q->where('event', $this->kind))
            ->when($this->staff !== '' && ctype_digit($this->staff), fn ($q) => $q->where('user_id', (int) $this->staff))
            ->latestFirst()
            ->paginate(25);

        return view('livewire.admin.logs.index', [
            'logs' => $logs,
            'events' => ActivityEvent::cases(),
            'staffUsers' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
