<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Meeting;
use App\Models\ThesisSupervision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Get complete student dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Profil mahasiswa belum lengkap.',
            ], 404);
        }

        $supervision = $student->supervision;

        // Submissions & stats
        $submissions = $supervision
            ? $supervision->submissions()->orderBy('submitted_at', 'desc')->get()
            : collect();

        $stats = [
            ['label' => 'Total Bimbingan',   'value' => (string) $submissions->count(),                        'color' => 'blue',   'icon' => 'file-text'],
            ['label' => 'Menunggu Feedback',  'value' => (string) $submissions->where('status', 'pending')->count(),  'color' => 'yellow', 'icon' => 'clock'],
            ['label' => 'Selesai',            'value' => (string) $submissions->where('status', 'approved')->count(), 'color' => 'green',  'icon' => 'check-circle'],
            ['label' => 'Revisi',             'value' => (string) $submissions->where('status', 'revision')->count(), 'color' => 'red',    'icon' => 'alert-circle'],
        ];

        // Thesis progress
        $totalMilestones = 6;
        $approvedMilestoneCount = $submissions
            ->where('status', 'approved')
            ->whereIn('type', ['Bab', 'Proposal'])
            ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x'))
            ->unique()
            ->count();
        $thesisProgress = min(100, (int) round($approvedMilestoneCount / $totalMilestones * 100));

        // Update the persisted progress column so it stays in sync
        if ($supervision && $supervision->progress !== $thesisProgress) {
            $supervision->update(['progress' => $thesisProgress]);
        }

        // Thesis title & supervisors
        $thesisTitle = $supervision?->title ?? 'Belum ditentukan';
        $supervisorNames = 'Belum ditentukan';
        $names = $student->supervisions
            ->map(fn($sv) => $sv->lecturer?->user?->name)
            ->filter()
            ->values();
        if ($names->isNotEmpty()) {
            $supervisorNames = $names->join(', ');
        }

        // Recent activities
        $statusMap = [
            'approved' => ['label' => 'Disetujui',       'type' => 'success'],
            'revision' => ['label' => 'Perlu Revisi',    'type' => 'warning'],
            'pending'  => ['label' => 'Menunggu Review',  'type' => 'pending'],
            'rejected' => ['label' => 'Ditolak',         'type' => 'error'],
        ];

        $recentActivities = $submissions->take(5)->map(function ($sub) use ($statusMap) {
            $status = $statusMap[$sub->status] ?? $statusMap['pending'];
            $decision = $sub->decision;
            return [
                'id'       => $sub->id,
                'title'    => $sub->title,
                'status'   => $status['label'],
                'date'     => $sub->submitted_at->translatedFormat('d F Y'),
                'feedback' => $decision ? $decision->feedback : '',
                'type'     => $status['type'],
            ];
        })->values()->all();

        // Meetings
        $meetings = $supervision
            ? $supervision->meetings()->orderBy('date', 'desc')->get()
            : collect();

        $allMeetings = $meetings->map(function ($m) use ($supervision) {
            return [
                'id'       => $m->id,
                'title'    => $m->title,
                'lecturer' => $supervision ? $supervision->lecturer->user->name : '',
                'date'     => $m->date->format('Y-m-d'),
                'time'     => $m->time_start,
                'location' => $m->location,
                'type'     => $m->type,
                'status'   => $m->status,
            ];
        })->values()->all();

        // This week meetings
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $thisWeekMeetings = collect($allMeetings)->filter(function ($m) use ($weekStart, $weekEnd) {
            $date = strtotime($m['date']);
            return $date >= $weekStart->timestamp && $date <= $weekEnd->timestamp && $m['status'] !== 'completed';
        })->values()->all();

        // Supervisor options for submission form
        $supervisorOptions = $student->supervisions()->with('lecturer.user')->get()->map(function ($s) {
            return [
                'id'         => $s->id,
                'name'       => $s->lecturer->user->name,
                'department' => $s->lecturer->department,
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data'    => [
                'studentName'       => $user->name,
                'thesisTitle'       => $thesisTitle,
                'supervisorNames'   => $supervisorNames,
                'thesisProgress'    => $thesisProgress,
                'stats'             => $stats,
                'recentActivities'  => $recentActivities,
                'allMeetings'       => $allMeetings,
                'thisWeekMeetings'  => $thisWeekMeetings,
                'supervisorOptions' => $supervisorOptions,
            ],
        ]);
    }
}
                
           
