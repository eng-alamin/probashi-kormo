<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::auth')]
#[Title('Admin Login')]
class Login extends Component
{
    public LoginForm $form;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute('admin.dashboard');
        }
    }

    public function login(): void
    {
        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('admin.dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
