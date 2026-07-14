<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\Meeting;
use App\Models\Submission;
use Livewire\Component;

class LecturerDashboard extends Component
{
    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            session(['role' => $user->role]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $lecturer = $user ? $user->lecturer : null;
        $supervisions = $lecturer
            ? $lecturer->supervisions()->with(['student.user', 'submissions'])->get()
            : collect();
        $supervisionIds = $supervisions->pluck('id');

        $allSubmissions = Submission::whereIn('supervision_id', $supervisionIds)->get();
        $pendingThisWeek = Submission::whereIn('supervision_id', $supervisionIds)
            ->where('submitted_at', '>=', now()->startOfWeek())
            ->get();

        $stats = [
            ['label' => 'Total Mahasiswa Bimbingan', 'value' => (string) $supervisions->count(),                                                                                                  'color' => 'blue',   'icon' => 'users'],
            ['label' => 'Menunggu Review',            'value' => (string) $allSubmissions->where('status', 'pending')->count(),                                                                   'color' => 'yellow', 'icon' => 'clock'],
            ['label' => 'Direview Minggu Ini',        'value' => (string) $pendingThisWeek->count(),                                                                                              'color' => 'green',  'icon' => 'file-text'],
            ['label' => 'Selesai Bulan Ini',          'value' => (string) $allSubmissions->where('status', 'approved')->filter(fn($s) => $s->updated_at >= now()->startOfMonth())->count(),      'color' => 'purple', 'icon' => 'check-circle'],
        ];

        // ── Build student list with auto-calculated progress ────────────────────
        $students = $supervisions->map(function ($sup) {
            $pendingCount    = $sup->submissions->where('status', 'pending')->count();
            $latestSubmission = $sup->submissions->sortByDesc('submitted_at')->first();

            // Calculate progress: approved unique BAB/Proposal milestones / 6
            $approvedCount = $sup->submissions
                ->where('status', 'approved')
                ->whereIn('type', ['Bab', 'Proposal'])
                ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x'))
                ->unique()
                ->count();
            $calculatedProgress = min(100, (int) round($approvedCount / 6 * 100));

            return [
                'id'             => $sup->student->id,
                'name'           => $sup->student->user->name,
                'nim'            => $sup->student->nim ?? '-',
                'title'          => $sup->title,
                'progress'       => $calculatedProgress,
                'pendingReviews' => $pendingCount,
                'lastActivity'   => $latestSubmission
                    ? $latestSubmission->submitted_at->diffForHumans()
                    : $sup->created_at->diffForHumans(),
                'status'         => $sup->status,
            ];
        })->values()->all();

        // ── Pending submissions needing review ──────────────────────────────────
        $pendingSubmissions = Submission::whereIn('supervision_id', $supervisionIds)
            ->where('status', 'pending')
            ->with(['supervision.student.user'])
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($sub) {
                $hoursAgo = $sub->submitted_at->diffInHours(now());
                $priority = $hoursAgo < 6 ? 'high' : ($hoursAgo < 48 ? 'medium' : 'low');

                return [
                    'id'          => $sub->id,
                    'student'     => $sub->supervision->student->user->name,
                    'title'       => $sub->title,
                    'submittedAt' => $sub->submitted_at->diffForHumans(),
                    'priority'    => $priority,
                ];
            })->values()->all();

        // ── Lecturer name for greeting ──────────────────────────────────────────
        $lecturerName = $user?->name ?? 'Dosen';

        // ── This week's confirmed meetings count for reminder ───────────────────
        $thisWeekMeetingsCount = Meeting::whereIn('supervision_id', $supervisionIds)
            ->where('status', 'confirmed')
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->count();

        return view('livewire.lecturer-dashboard', compact(
            'stats',
            'students',
            'pendingSubmissions',
            'lecturerName',
            'thisWeekMeetingsCount'
        ));
    }
}
