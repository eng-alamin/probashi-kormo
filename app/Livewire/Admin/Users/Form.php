<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Enums\ActivityEvent;
use App\Enums\RoleName;
use App\Models\User;
use App\Support\ActivityLogger;
use App\Support\StaffAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::admin')]
class Form extends Component
{
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = '';

    public bool $is_active = true;

    public function mount(?User $user = null): void
    {
        if ($user === null || ! $user->exists) {
            $this->authorize('users.create');
            $this->role = RoleName::Staff->value;

            return;
        }

        $this->authorize('users.update');
        abort_unless(StaffAccess::canManage(auth()->user(), $user), 403);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = (string) $user->roles->first()?->name;
        $this->is_active = $user->is_active;
    }

    private function isSelf(): bool
    {
        return $this->userId !== null && $this->userId === auth()->id();
    }

    /**
     * Roles shown in the dropdown. When editing a Super Admin the list always includes that role,
     * so the current value is never lost.
     *
     * @return list<string>
     */
    private function roleOptions(): array
    {
        return StaffAccess::assignableRoles(auth()->user());
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $password = Password::min(10)->letters()->numbers();

        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email:rfc', 'max:190', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => [$this->userId === null ? 'required' : 'nullable', 'string', 'confirmed', $password],
            'role' => ['required', Rule::in($this->roleOptions())],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'role.in' => 'You cannot give this role.',
            'password.confirmed' => 'The two passwords do not match.',
        ];
    }

    public function save(): void
    {
        $this->authorize($this->userId === null ? 'users.create' : 'users.update');

        $this->name = trim($this->name);
        $this->email = strtolower(trim($this->email));

        $target = $this->userId === null ? null : User::findOrFail($this->userId);

        if ($target !== null) {
            abort_unless(StaffAccess::canManage(auth()->user(), $target), 403);

            // On your own account the role and the active switch are locked, so you cannot lock yourself out.
            if ($this->isSelf()) {
                $this->role = (string) $target->roles->first()?->name;
                $this->is_active = true;
            }
        }

        // Your own Super Admin role stays valid even though the dropdown rule is built from "assignable" roles.
        $this->validate();

        if ($target !== null && StaffAccess::isLastActiveSuperAdmin($target)
            && ($this->role !== RoleName::SuperAdmin->value || ! $this->is_active)) {
            $this->addError('role', 'There must be at least one active Super Admin.');

            return;
        }

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->password !== '') {
            $payload['password'] = $this->password;
        }

        if ($target === null) {
            $user = User::create($payload);
            $user->syncRoles([$this->role]);
            $this->logRoleChange($user, '');
            $message = 'Account created';
        } else {
            $previousRole = (string) $target->roles->first()?->name;

            $target->update($payload);
            $target->syncRoles([$this->role]);
            $this->logRoleChange($target, $previousRole);
            $message = 'Account updated';
        }

        session()->flash('toast', $message);

        $this->redirectRoute('admin.users.index');
    }

    /**
     * Records who got which role. Nothing is written when the role did not change.
     */
    private function logRoleChange(User $user, string $previousRole): void
    {
        if ($previousRole === $this->role) {
            return;
        }

        $description = $previousRole === ''
            ? "Gave role {$this->role} to staff user \"{$user->name}\""
            : "Changed role of staff user \"{$user->name}\" from {$previousRole} to {$this->role}";

        ActivityLogger::record(ActivityEvent::RoleChanged, $description, 'user', (int) $user->id);
    }

    public function render(): View
    {
        return view('livewire.admin.users.form', [
            'isEdit' => $this->userId !== null,
            'isSelf' => $this->isSelf(),
            'roleOptions' => $this->roleOptions(),
        ])->title($this->userId === null ? 'New staff user' : 'Edit staff user');
    }
}
