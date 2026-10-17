<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Enums\RoleName;
use App\Models\User;
use App\Support\StaffAccess;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin')]
#[Title('Staff Users')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $role = '';

    public ?int $deletingId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    /**
     * Returns the target user, or null (after showing a message) when the action is not allowed.
     */
    private function guardedTarget(int $id, string $action): ?User
    {
        $target = User::findOrFail($id);

        abort_unless(StaffAccess::canManage(auth()->user(), $target), 403);

        if ($target->is(auth()->user())) {
            $this->dispatch('toast', message: "You cannot {$action} your own account.", type: 'error');

            return null;
        }

        if (StaffAccess::isLastActiveSuperAdmin($target)) {
            $this->dispatch('toast', message: "You cannot {$action} the last active Super Admin.", type: 'error');

            return null;
        }

        return $target;
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('users.update');

        $target = User::findOrFail($id);

        // Switching someone back on is always safe; only switching off needs the checks.
        if (! $target->is_active) {
            abort_unless(StaffAccess::canManage(auth()->user(), $target), 403);
            $target->update(['is_active' => true]);
            $this->dispatch('toast', message: 'Account activated', type: 'ok');

            return;
        }

        $target = $this->guardedTarget($id, 'deactivate');

        if ($target === null) {
            return;
        }

        $target->update(['is_active' => false]);
        $this->dispatch('toast', message: 'Account deactivated. They are signed out on their next click.', type: 'ok');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('users.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function delete(): void
    {
        $this->authorize('users.delete');

        if ($this->deletingId !== null) {
            $target = $this->guardedTarget($this->deletingId, 'delete');

            if ($target !== null) {
                $target->delete();
                $this->dispatch('toast', message: 'Account deleted', type: 'ok');
            }
        }

        $this->deletingId = null;
    }

    public function render(): View
    {
        $this->authorize('users.view');

        $term = trim($this->search);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $term).'%';

        $users = User::query()
            ->with('roles:id,name')
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like)))
            ->when(in_array($this->role, RoleName::values(), true), fn ($q) => $q->role($this->role))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.users.index', [
            'users' => $users,
            'roleNames' => RoleName::values(),
        ]);
    }
}
