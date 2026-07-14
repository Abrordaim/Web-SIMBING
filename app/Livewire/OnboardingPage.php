<?php

namespace App\Livewire;

use App\Models\Lecturer;
use App\Models\Student;
use App\Mail\WelcomeNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class OnboardingPage extends Component
{
    public int $step = 1;
    public string $role = 'student';

    // Student fields
    public string $nim = '';
    public string $semester = '1';
    public string $department = '';
    public string $faculty = '';

    // Lecturer fields
    public string $nidn = '';
    public string $specialization = '';

    public function mount()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // If user already has a complete profile, redirect to dashboard
        if ($user->isStudent() && $user->student) {
            return redirect('/student');
        }
        if ($user->isLecturer() && $user->lecturer) {
            return redirect('/lecturer');
        }

        // Pre-fill role from existing user record
        $this->role = $user->role ?? 'student';
    }

    public function selectRole(string $role): void
    {
        $this->role = $role;
        // Reset fields when switching role
        $this->nim = '';
        $this->nidn = '';
        $this->department = '';
        $this->faculty = '';
        $this->specialization = '';
    }

    public function nextStep(): void
    {
        $this->validate([
            'role' => 'required|in:student,lecturer',
        ]);
        $this->step = 2;
    }

    public function prevStep(): void
    {
        $this->step = 1;
    }

    public function complete()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        if ($this->role === 'student') {
            $this->validate([
                'nim'        => 'nullable|string|max:20',
                'semester'   => 'required|integer|min:1|max:14',
                'department' => 'nullable|string|max:100',
                'faculty'    => 'nullable|string|max:100',
            ]);

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nim'        => $this->nim ?: null,
                    'semester'   => (int) $this->semester,
                    'department' => $this->department ?: null,
                    'faculty'    => $this->faculty ?: null,
                ]
            )->update([
                'nim'        => $this->nim ?: null,
                'semester'   => (int) $this->semester,
                'department' => $this->department ?: null,
                'faculty'    => $this->faculty ?: null,
            ]);
        } else {
            $this->validate([
                'nidn'           => 'nullable|string|max:20',
                'department'     => 'nullable|string|max:100',
                'faculty'        => 'nullable|string|max:100',
                'specialization' => 'nullable|string|max:100',
            ]);

            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nidn'           => $this->nidn ?: null,
                    'department'     => $this->department ?: null,
                    'faculty'        => $this->faculty ?: null,
                    'specialization' => $this->specialization ?: null,
                ]
            )->update([
                'nidn'           => $this->nidn ?: null,
                'department'     => $this->department ?: null,
                'faculty'        => $this->faculty ?: null,
                'specialization' => $this->specialization ?: null,
            ]);
        }

        // Update user role
        $user->update(['role' => $this->role]);
        session(['role' => $this->role]);

        // Kirim welcome email (queued, tidak blokir UI)
        $user->load(['student', 'lecturer']);
        Mail::to($user->email)->queue(new WelcomeNewUser($user));

        return redirect($this->role === 'lecturer' ? '/lecturer' : '/student');
    }

    public function render()
    {
        return view('livewire.onboarding-page')
            ->layout('components.layouts.guest');
    }
}
