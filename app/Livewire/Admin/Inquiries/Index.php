<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Inquiries;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Inquiries')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('inquiries.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('inquiries.delete');

        if ($this->deletingId !== null) {
            Inquiry::find($this->deletingId)?->delete();
            $this->dispatch('toast', message: 'Inquiry deleted', type: 'ok');
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('inquiries.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $inquiries = Inquiry::query()
            ->with('country:id,name')
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('message', 'like', $like)))
            ->when(in_array($this->status, InquiryStatus::values(), true), fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.inquiries.index', [
            'inquiries' => $inquiries,
            'statuses' => InquiryStatus::cases(),
        ]);
    }
}
