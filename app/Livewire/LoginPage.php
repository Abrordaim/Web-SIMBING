<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public string $errorMessage = '';

    public function login()
    {
        $this->errorMessage = '';

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            $user = Auth::user();
            session(['role' => $user->role]);

            return $this->redirect($user->role === 'lecturer' ? '/lecturer' : '/student');
        }

        $this->errorMessage = 'Email atau password salah.';
    }

    public function render()
    {
        return view('livewire.login-page')
            ->layout('components.layouts.guest');
    }
}
