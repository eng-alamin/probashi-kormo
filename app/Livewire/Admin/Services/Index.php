<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Services')]
class Index extends Component
{
    #[Url(except: '')]
    public string $search = '';

    public ?int $deletingId = null;

    public function toggleActive(int $id): void
    {
        $this->authorize('services.update');

        $service = Service::findOrFail($id);
        $service->update(['is_active' => ! $service->is_active]);
        $this->dispatch('toast', message: $service->is_active ? 'Service is now visible on the website' : 'Service hidden from the website', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('services.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('services.delete');

        if ($this->deletingId !== null) {
            Service::find($this->deletingId)?->delete();
            $this->dispatch('toast', message: 'Service deleted', type: 'ok');
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('services.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $services = Service::query()
            ->when($term !== '', fn ($q) => $q->where('title', 'like', $like))
            ->ordered()
            ->get();

        return view('livewire.admin.services.index', ['services' => $services]);
    }
}
