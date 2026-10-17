<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Country;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Applications')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $country = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCountry(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('applications.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('applications.delete');

        if ($this->deletingId !== null) {
            JobApplication::find($this->deletingId)?->delete();
            $this->dispatch('toast', message: 'Application deleted', type: 'ok');
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('applications.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $applications = JobApplication::query()
            ->with(['jobPost:id,title', 'country:id,name'])
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', $like)
                ->orWhere('phone', 'like', $like)))
            ->when(in_array($this->status, ApplicationStatus::values(), true), fn ($q) => $q->where('status', $this->status))
            ->when($this->country !== '' && ctype_digit($this->country), fn ($q) => $q->where('country_id', (int) $this->country))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.applications.index', [
            'applications' => $applications,
            'statuses' => ApplicationStatus::cases(),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
