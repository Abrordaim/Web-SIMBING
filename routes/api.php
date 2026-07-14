<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LecturerDashboardController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RevisionController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

// ── Public Routes (no authentication required) ─────────────────────────────
Route::post('/login',        [AuthController::class, 'login']);
Route::post('/register',     [AuthController::class, 'register']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);

// ── Protected Routes (require Sanctum token) ───────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',     [AuthController::class, 'logout']);
    Route::get('/user',        [AuthController::class, 'me']);
    Route::post('/onboarding', [AuthController::class, 'onboarding']);

    // Profile
    Route::get('/profile',  [ProfileController::class, 'show']);
    Route::put('/profile',  [ProfileController::class, 'update']);
    Route::post('/push-token', [ProfileController::class, 'savePushToken']);

    // Student Dashboard
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->middleware('role:student');

    // Lecturer Dashboard
    Route::get('/lecturer/dashboard', [LecturerDashboardController::class, 'index'])
        ->middleware('role:lecturer');

    // Submissions
    Route::get('/submissions',      [SubmissionController::class, 'index']);
    Route::post('/submissions',     [SubmissionController::class, 'store'])
        ->middleware('role:student');
    Route::get('/submissions/{id}', [SubmissionController::class, 'show']);

    // Meetings
    Route::get('/meetings',               [MeetingController::class, 'index']);
    Route::put('/meetings/{id}',          [MeetingController::class, 'update']);
    Route::post('/meetings/{id}/confirm', [MeetingController::class, 'confirm'])
        ->middleware('role:lecturer');
    Route::post('/meetings/{id}/reject',  [MeetingController::class, 'reject'])
        ->middleware('role:lecturer');
    Route::post('/meetings/{id}/cancel',  [MeetingController::class, 'cancel'])
        ->middleware('role:student');

    // Revisions & Feedback
    Route::get('/revisions',                       [RevisionController::class, 'index']);
    Route::post('/revisions/{id}/comment',         [RevisionController::class, 'addComment']);
    Route::post('/revisions/{id}/toggle-resolve',  [RevisionController::class, 'toggleResolve'])
        ->middleware('role:lecturer');
    Route::post('/revisions/{id}/decision',        [RevisionController::class, 'submitDecision'])
        ->middleware('role:lecturer');
});

