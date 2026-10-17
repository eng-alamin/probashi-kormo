<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Inquiries;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Inquiry')]
class Show extends Component
{
    public Inquiry $inquiry;

    public string $status = '';

    public bool $confirmingDelete = false;

    public function mount(Inquiry $inquiry): void
    {
        $this->authorize('inquiries.view');

        // Opening a new message marks it as read, but only for people allowed to edit it.
        if ($inquiry->status === InquiryStatus::New && auth()->user()->can('inquiries.update')) {
            $inquiry->update(['status' => InquiryStatus::Read, 'read_at' => now()]);
        }

        $this->inquiry = $inquiry->load('country:id,name');
        $this->status = $inquiry->status->value;
    }

    public function save(): void
    {
        $this->authorize('inquiries.update');

        $data = $this->validate([
            'status' => ['required', Rule::in(InquiryStatus::values())],
        ]);

        $this->inquiry->update([
            'status' => $data['status'],
            'read_at' => $this->inquiry->read_at ?? now(),
        ]);

        $this->dispatch('toast', message: 'Inquiry updated', type: 'ok');
    }

    public function delete(): void
    {
        $this->authorize('inquiries.delete');

        $this->inquiry->delete();
        session()->flash('toast', 'Inquiry deleted');

        $this->redirectRoute('admin.inquiries.index');
    }

    public function render(): View
    {
        return view('livewire.admin.inquiries.show', [
            'statuses' => InquiryStatus::cases(),
        ]);
    }
}
