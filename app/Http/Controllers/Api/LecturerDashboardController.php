<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LecturerDashboardController extends Controller
{
    /**
     * Get complete lecturer dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $lecturer = $user->lecturer;

        if (! $lecturer) {
            return response()->json([
                'success' => false,
                'message' => 'Profil dosen belum lengkap.',
            ], 404);
        }

        $supervisions = $lecturer->supervisions()->with(['student.user', 'submissions'])->get();

        // Student list
        $students = $supervisions->map(function ($sup) {
            $submissions = $sup->submissions;
            $latestSubmission = $submissions->sortByDesc('submitted_at')->first();

            // Calculate progress dynamically: approved unique Bab/Proposal milestones out of 6
            $approvedCount = $submissions
                ->where('status', 'approved')
                ->whereIn('type', ['Bab', 'Proposal'])
                ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x'))
                ->unique()
                ->count();
            $calculatedProgress = min(100, (int) round($approvedCount / 6 * 100));

            // Update the persisted progress column so it stays in sync
            if ($sup->progress !== $calculatedProgress) {
                $sup->update(['progress' => $calculatedProgress]);
            }

            return [
                'id'       => $sup->student->id,
                'name'     => $sup->student->user->name,
                'nim'      => $sup->student->nim,
                'avatar'   => $sup->student->user->avatar_url,
                'initials' => $sup->student->user->initials,
                'title'    => $sup->title,
                'progress' => $calculatedProgress,
                'pending'  => $submissions->where('status', 'pending')->count(),
                'last'     => $latestSubmission
                    ? $latestSubmission->submitted_at->diffForHumans(short: true)
                    : 'N/A',
            ];
        })->values()->all();

        // Pending submissions (need review)
        $supervisionIds = $supervisions->pluck('id');
        $pendingSubmissions = Submission::whereIn('supervision_id', $supervisionIds)
            ->where('status', 'pending')
            ->with(['supervision.student.user'])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($sub) {
                $hoursAgo = $sub->submitted_at->diffInHours(now());
                $priority = $hoursAgo > 48 ? 'high' : ($hoursAgo > 24 ? 'medium' : 'low');

                return [
                    'id'         => $sub->id,
                    'title'      => $sub->title,
                    'student'    => $sub->supervision->student->user->name,
                    'student_id' => $sub->supervision->student->id,
                    'time'       => $sub->submitted_at->diffForHumans(short: true),
                    'priority'   => $priority,
                ];
            })->values()->all();

        // Stats
        $totalSubmissions = Submission::whereIn('supervision_id', $supervisionIds)->count();
        $reviewedCount = Submission::whereIn('supervision_id', $supervisionIds)
            ->whereIn('status', ['approved', 'revision', 'rejected'])->count();

        $stats = [
            ['label' => 'Mahasiswa Bimbingan', 'value' => (string) count($students), 'color' => 'blue',   'icon' => 'users'],
            ['label' => 'Menunggu Review',     'value' => (string) count($pendingSubmissions), 'color' => 'yellow', 'icon' => 'clock'],
            ['label' => 'Total Dokumen',       'value' => (string) $totalSubmissions,   'color' => 'green',  'icon' => 'file-text'],
            ['label' => 'Telah Direview',      'value' => (string) $reviewedCount,      'color' => 'purple', 'icon' => 'check-circle'],
        ];

        // Upcoming meetings
        $meetings = $lecturer->supervisions()
            ->with(['meetings' => fn($q) => $q->whereIn('status', ['confirmed', 'pending'])->orderBy('date')])
            ->get()
            ->pluck('meetings')
            ->flatten()
            ->take(5)
            ->map(function ($m) {
                return [
                    'id'       => $m->id,
                    'title'    => $m->title,
                    'date'     => $m->date->format('Y-m-d'),
                    'time'     => $m->time_start,
                    'status'   => $m->status,
                    'student'  => $m->supervision->student->user->name ?? '',
                ];
            })->values()->all();

        $thisWeekMeetingsCount = \App\Models\Meeting::whereIn('supervision_id', $supervisionIds)
            ->where('status', 'confirmed')
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'lecturerName'          => $user->name,
                'stats'                 => $stats,
                'students'              => $students,
                'pendingSubmissions'    => $pendingSubmissions,
                'upcomingMeetings'      => $meetings,
                'thisWeekMeetingsCount' => $thisWeekMeetingsCount,
            ],
        ]);
    }
}
