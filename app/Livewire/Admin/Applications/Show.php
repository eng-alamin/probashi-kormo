<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::admin')]
#[Title('Application')]
class Show extends Component
{
    public JobApplication $application;

    public string $status = '';

    public string $admin_note = '';

    public bool $confirmingDelete = false;

    public function mount(JobApplication $application): void
    {
        $this->authorize('applications.view');

        $this->application = $application->load(['jobPost:id,title,slug', 'country:id,name', 'handledBy:id,name']);
        $this->status = $application->status->value;
        $this->admin_note = (string) $application->admin_note;
    }

    public function save(): void
    {
        $this->authorize('applications.update');

        $data = $this->validate([
            'status' => ['required', Rule::in(ApplicationStatus::values())],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->application->update([
            'status' => $data['status'],
            'admin_note' => $data['admin_note'] !== '' ? $data['admin_note'] : null,
            'handled_by' => auth()->id(),
        ]);

        $this->application->load('handledBy:id,name');
        $this->dispatch('toast', message: 'Application updated', type: 'ok');
    }

    public function delete(): void
    {
        $this->authorize('applications.delete');

        $this->application->delete();
        session()->flash('toast', 'Application deleted');

        $this->redirectRoute('admin.applications.index');
    }

    public function render(): View
    {
        return view('livewire.admin.applications.show', [
            'statuses' => ApplicationStatus::cases(),
        ]);
    }
}
