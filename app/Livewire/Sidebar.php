<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    public string $role = 'student';

    public function mount()
    {
        $user = Auth::user();
        $this->role = $user ? $user->role : session('role', 'student');
    }

    public function toggleRole()
    {
        $this->role = $this->role === 'student' ? 'lecturer' : 'student';
        session(['role' => $this->role]);

        return $this->redirect($this->role === 'student' ? '/student' : '/lecturer');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/login');
    }

    public function render()
    {
        $user = Auth::user();

        $menuItems = [
            ['path' => $this->role === 'student' ? '/student' : '/lecturer', 'label' => 'Dashboard', 'icon' => 'home'],
        ];

        if ($this->role === 'student') {
            $menuItems[] = ['path' => '/submission', 'label' => 'Pengajuan Bimbingan', 'icon' => 'file-text'];
        }

        $menuItems[] = ['path' => '/revision', 'label' => 'Revisi & Feedback', 'icon' => 'message-square'];
        $menuItems[] = ['path' => '/schedule', 'label' => 'Jadwal Konsultasi', 'icon' => 'calendar'];
        $menuItems[] = ['path' => '/profile', 'label' => 'Profil', 'icon' => 'user'];

        return view('livewire.sidebar', [
            'menuItems' => $menuItems,
            'userName' => $user?->name ?? 'User',
            'userAvatar' => $user?->avatar_url,
            'userInitials' => $user?->initials ?? 'U',
            'userRoleLabel' => $this->role === 'student' ? 'Mahasiswa' : 'Dosen',
        ]);
    }
}
