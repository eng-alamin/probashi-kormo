<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Countries;

use App\Models\Country;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Countries')]
class Index extends Component
{
    #[Url(except: '')]
    public string $search = '';

    public ?int $deletingId = null;

    public function toggleActive(int $id): void
    {
        $this->authorize('countries.update');

        $country = Country::findOrFail($id);
        $country->update(['is_active' => ! $country->is_active]);
        $this->dispatch('toast', message: $country->is_active ? 'Country is now visible on the website' : 'Country hidden from the website', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('countries.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('countries.delete');

        if ($this->deletingId === null) {
            return;
        }

        $country = Country::withCount('jobPosts')->find($this->deletingId);
        $this->deletingId = null;

        if ($country === null) {
            return;
        }

        // The database also blocks this (restrictOnDelete); checking first gives a clear message.
        if ($country->job_posts_count > 0) {
            $this->dispatch('toast', message: 'This country still has jobs. Delete or move them first, or hide the country instead.', type: 'error');

            return;
        }

        $country->delete();
        $this->dispatch('toast', message: 'Country deleted', type: 'ok');
    }

    public function render(): View
    {
        $this->authorize('countries.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $countries = Country::query()
            ->withCount('jobPosts')
            ->when($term !== '', fn ($q) => $q->where('name', 'like', $like))
            ->ordered()
            ->get();

        return view('livewire.admin.countries.index', ['countries' => $countries]);
    }
}
