<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Jobs;

use App\Models\Country;
use App\Models\JobPost;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Jobs')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $country = '';

    /** open | closed | inactive */
    #[Url(except: '')]
    public string $state = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCountry(): void
    {
        $this->resetPage();
    }

    public function updatedState(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('jobs.update');

        $job = JobPost::findOrFail($id);
        $job->update(['is_active' => ! $job->is_active]);
        $this->dispatch('toast', message: $job->is_active ? 'Job is now visible on the website' : 'Job hidden from the website', type: 'ok');
    }

    public function toggleFeatured(int $id): void
    {
        $this->authorize('jobs.update');

        $job = JobPost::findOrFail($id);
        $job->update(['is_featured' => ! $job->is_featured]);
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('jobs.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('jobs.delete');

        if ($this->deletingId !== null) {
            JobPost::find($this->deletingId)?->delete();
            $this->dispatch('toast', message: 'Job deleted', type: 'ok');
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('jobs.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $jobs = JobPost::query()
            ->with('country:id,name')
            ->withCount('applications')
            ->when($term !== '', fn ($q) => $q->where('title', 'like', $like))
            ->when($this->country !== '' && ctype_digit($this->country), fn ($q) => $q->where('country_id', (int) $this->country))
            ->when($this->state === 'open', fn ($q) => $q->open())
            ->when($this->state === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->state === 'closed', fn ($q) => $q->where('is_active', true)->whereNotNull('deadline')->where('deadline', '<', today()->toDateString()))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.jobs.index', [
            'jobs' => $jobs,
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
