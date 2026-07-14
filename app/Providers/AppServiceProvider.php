<?php

namespace App\Providers;

use App\Models\Meeting;
use App\Models\Submission;
use App\Models\ThesisSupervision;
use App\Policies\MeetingPolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\ThesisSupervisionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Register Policies ─────────────────────────────────────────────
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Meeting::class, MeetingPolicy::class);
        Gate::policy(ThesisSupervision::class, ThesisSupervisionPolicy::class);

        // ── Role-based Gates ──────────────────────────────────────────────
        Gate::define('is-student', fn($user) => $user->role === 'student');
        Gate::define('is-lecturer', fn($user) => $user->role === 'lecturer');

        // ── Action-based Gates ────────────────────────────────────────────
        // Aksi yang membutuhkan role tertentu (tanpa model spesifik)
        Gate::define('submit-thesis', fn($user) => $user->isStudent());
        Gate::define('review-submission', fn($user) => $user->isLecturer());
        Gate::define('view-student-detail', fn($user) => $user->isLecturer());
        Gate::define('manage-schedule', fn($user) => in_array($user->role, ['student', 'lecturer']));
        Gate::define('send-comment', fn($user) => in_array($user->role, ['student', 'lecturer']));
    }
}
