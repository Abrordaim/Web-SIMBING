<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Auto generate name from email if not provided
        $name = $this->name ?: explode('@', $this->email)[0];

        $user = User::create([
            'name'     => $name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => 'student', // default, user can change at onboarding
        ]);

        // Automatically login
        Auth::login($user);
        session()->regenerate();
        session(['role' => $user->role]);

        // Redirect to onboarding to choose role and fill in profile
        return $this->redirect('/onboarding');
    }

    public function render()
    {
        return view('livewire.register-page')
            ->layout('components.layouts.guest');
    }
}
