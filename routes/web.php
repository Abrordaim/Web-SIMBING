<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Guest-only routes (redirect to dashboard if already logged in) ──────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    \App\Livewire\LoginPage::class)->name('login');
    Route::get('/register', \App\Livewire\RegisterPage::class)->name('register');
});

// ── Google OAuth (tidak perlu auth/guest middleware) ─────────────────────────
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// ── Authenticated routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Root redirect — pakai Auth::user() bukan session
    Route::get('/', function () {
        $role = Auth::user()->role;
        return redirect($role === 'lecturer' ? '/lecturer' : '/student');
    });

    // Onboarding & Profile (semua role yang sudah login)
    Route::get('/onboarding', \App\Livewire\OnboardingPage::class)->name('onboarding');
    Route::get('/profile',    \App\Livewire\ProfilePage::class)->name('profile');

    // ── Student-only routes ───────────────────────────────────────────────────
    Route::middleware('role:student')->group(function () {
        Route::get('/student',    \App\Livewire\StudentDashboard::class)->name('student.dashboard');
        Route::get('/submission', \App\Livewire\ThesisSubmission::class)->name('submission');
    });

    // ── Lecturer-only routes ──────────────────────────────────────────────────
    Route::middleware('role:lecturer')->group(function () {
        Route::get('/lecturer',            \App\Livewire\LecturerDashboard::class)->name('lecturer.dashboard');
        Route::get('/student-detail/{id}', \App\Livewire\StudentDetailPage::class)->name('student.detail');
    });

    // ── Shared routes (student & lecturer) ───────────────────────────────────
    Route::middleware('role:student,lecturer')->group(function () {
        Route::get('/revision', \App\Livewire\RevisionFeedback::class)->name('revision');
        Route::get('/schedule', \App\Livewire\ScheduleMeeting::class)->name('schedule');
    });
});

