<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Submission;
use App\Models\ThesisSupervision;
use App\Models\SubmissionDecision;
use App\Models\TimelineEvent;
use App\Mail\SubmissionDecisionNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RevisionController extends Controller
{
    /**
     * Get revision threads.
     * Student: own submissions with comments.
     * Lecturer: student list, then drill into a specific student.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            return $this->studentThreads($request);
        }

        if ($user->isLecturer()) {
            $studentId = $request->query('student_id');
            if ($studentId) {
                return $this->lecturerStudentThreads($request, (int) $studentId);
            }
            return $this->lecturerStudentList($request);
        }

        return response()->json(['success' => true, 'data' => []]);
    }

    /**
     * Student: get own revision threads.
     */
    private function studentThreads(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student || ! $student->supervision) {
            return response()->json(['success' => true, 'data' => ['threads' => []]]);
        }

        $submissions = $student->supervision->submissions()
            ->with(['comments.user', 'decision'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $threads = $this->mapSubmissionsToThreads($submissions);

        return response()->json([
            'success' => true,
            'data'    => ['threads' => $threads],
        ]);
    }

    /**
     * Lecturer: get list of students with summary.
     */
    private function lecturerStudentList(Request $request): JsonResponse
    {
        $user = $request->user();
        $lecturer = $user->lecturer;

        if (! $lecturer) {
            return response()->json(['success' => true, 'data' => ['students' => []]]);
        }

        $supervisions = $lecturer->supervisions()->with(['student.user'])->get();

        $students = $supervisions->map(function ($sup) {
            $submissions = $sup->submissions;
            $latestComment = Comment::whereIn('submission_id', $submissions->pluck('id'))
                ->latest()
                ->first();

            $unreadCount = $submissions->where('status', 'pending')->count();

            return [
                'id'            => $sup->student->id,
                'name'          => $sup->student->user->name,
                'nim'           => $sup->student->nim,
                'avatar'        => $sup->student->user->avatar_url,
                'initials'      => $sup->student->user->initials,
                'title'         => $sup->title,
                'unread'        => $unreadCount,
                'lastMsg'       => $latestComment ? \Str::limit($latestComment->text, 40) : 'Belum ada pesan',
                'lastTime'      => $latestComment ? $latestComment->created_at->diffForHumans(short: true) : '',
                'totalDocs'     => $submissions->count(),
                'pendingCount'  => $submissions->where('status', 'pending')->count(),
                'revisionCount' => $submissions->where('status', 'revision')->count(),
                'approvedCount' => $submissions->where('status', 'approved')->count(),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data'    => ['students' => $students],
        ]);
    }

    /**
     * Lecturer: get threads for a specific student.
     */
    private function lecturerStudentThreads(Request $request, int $studentId): JsonResponse
    {
        $user = $request->user();
        $lecturer = $user->lecturer;

        $supervision = ThesisSupervision::where('student_id', $studentId)
            ->where('lecturer_id', $lecturer->id)
            ->first();

        if (! $supervision) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        $submissions = $supervision->submissions()
            ->with(['comments.user', 'decision'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $threads = $this->mapSubmissionsToThreads($submissions);

        return response()->json([
            'success' => true,
            'data'    => ['threads' => $threads],
        ]);
    }

    /**
     * Add a comment to a submission thread.
     */
    public function addComment(Request $request, int $submissionId): JsonResponse
    {
        Gate::authorize('send-comment');

        $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $submission = Submission::findOrFail($submissionId);

        // Verify user is part of this supervision
        $supervision = $submission->supervision;
        if ($user->isStudent() && $supervision->student->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        if ($user->isLecturer() && $supervision->lecturer->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $comment = Comment::create([
            'submission_id' => $submissionId,
            'user_id'       => $user->id,
            'text'          => $request->text,
        ]);

        // Send Push Notification
        $targetUser = $user->isLecturer() 
            ? $supervision->student->user 
            : $supervision->lecturer->user;

        if ($targetUser) {
            \App\Services\PushNotificationService::send(
                $targetUser->expo_push_token,
                'Pesan Revisi Baru',
                $user->name . ': ' . \Str::limit($request->text, 40),
                ['url' => '/(tabs)/revision']
            );
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'comment' => [
                    'id'     => $comment->id,
                    'author' => $user->role === 'lecturer' ? 'lecturer' : 'student',
                    'name'   => $user->name,
                    'avatar' => $user->avatar_url,
                    'text'   => $comment->text,
                    'time'   => $comment->created_at->translatedFormat('d M Y, H:i'),
                ],
            ],
            'message' => 'Komentar berhasil dikirim.',
        ], 201);
    }

    /**
     * Toggle resolve status on a submission (lecturer only).
     */
    public function toggleResolve(Request $request, int $submissionId): JsonResponse
    {
        $submission = Submission::findOrFail($submissionId);
        Gate::authorize('resolve', $submission);

        $submission->update(['resolved' => ! $submission->resolved]);

        if ($submission->resolved) {
            $studentUser = $submission->supervision->student->user;
            \App\Services\PushNotificationService::send(
                $studentUser->expo_push_token,
                'Revisi Selesai',
                'Dosen telah menandai dokumen ' . $submission->title . ' sebagai selesai.',
                ['url' => '/(tabs)/revision']
            );
        }

        return response()->json([
            'success' => true,
            'data'    => ['resolved' => (bool) $submission->resolved],
            'message' => $submission->resolved ? 'Thread ditandai selesai.' : 'Thread dibuka kembali.',
        ]);
    }

    /**
     * Submit review decision for a submission.
     */
    public function submitDecision(Request $request, int $submissionId): JsonResponse
    {
        $request->validate([
            'decision' => 'required|string|in:approved,revision_minor,revision_major,rejected',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission = Submission::findOrFail($submissionId);

        // Check via Policy: only supervising lecturer can update
        Gate::authorize('update', $submission);

        $user = $request->user();

        // Create or update decision
        $decision = SubmissionDecision::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'lecturer_id' => $user->lecturer->id,
                'decision' => $request->decision,
                'feedback' => $request->feedback ?? '',
                'decided_at' => now(),
            ]
        );

        // Update submission status
        $statusMap = [
            'approved' => 'approved',
            'revision_minor' => 'revision',
            'revision_major' => 'revision',
            'rejected' => 'rejected',
        ];
        $newStatus = $statusMap[$request->decision] ?? 'pending';
        $submission->update(['status' => $newStatus]);

        // Calculate and update thesis progress
        $supervision = $submission->supervision;
        if ($supervision) {
            $approvedCount = $supervision->submissions
                ->where('status', 'approved')
                ->whereIn('type', ['Bab', 'Proposal'])
                ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x'))
                ->unique()
                ->count();
            $calculatedProgress = min(100, (int) round($approvedCount / 6 * 100));
            $supervision->update(['progress' => $calculatedProgress]);
        }

        // Create timeline event
        $decisionLabels = [
            'approved' => 'disetujui',
            'revision_minor' => 'perlu revisi minor',
            'revision_major' => 'perlu revisi mayor',
            'rejected' => 'ditolak',
        ];
        TimelineEvent::create([
            'supervision_id' => $submission->supervision_id,
            'event' => $submission->title . ' ' . ($decisionLabels[$request->decision] ?? ''),
            'type' => $request->decision === 'approved' ? 'approved' : 'revision',
            'event_date' => now()->toDateString(),
        ]);

        // Send Email notification to student (queued)
        $submission->load('supervision.student.user');
        $studentUser = $submission->supervision?->student?->user;
        if ($studentUser) {
            Mail::to($studentUser->email)->queue(
                new SubmissionDecisionNotification(
                    $submission,
                    $request->decision,
                    $request->feedback ?? '',
                    $studentUser,
                    $user
                )
            );

            // Send Push Notification
            $decisionDisplayLabels = [
                'approved' => 'Disetujui',
                'revision_minor' => 'Revisi Minor',
                'revision_major' => 'Revisi Mayor',
                'rejected' => 'Tidak Disetujui',
            ];
            \App\Services\PushNotificationService::send(
                $studentUser->expo_push_token,
                'Keputusan Review Baru',
                'Dosen memberikan keputusan "' . ($decisionDisplayLabels[$request->decision] ?? '') . '" untuk ' . $submission->title,
                ['url' => '/(tabs)/revision']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Keputusan berhasil dikirim.',
            'data' => [
                'status' => $newStatus,
                'decision' => [
                    'decision' => $decision->decision,
                    'feedback' => $decision->feedback,
                ]
            ]
        ]);
    }

    /**
     * Map submission models to thread format.
     */
    private function mapSubmissionsToThreads($submissions): array
    {
        return $submissions->map(function ($sub) {
            $comments = $sub->comments->map(function ($c) {
                return [
                    'id'     => $c->id,
                    'author' => $c->user->role === 'lecturer' ? 'lecturer' : 'student',
                    'name'   => $c->user->name,
                    'avatar' => $c->user->avatar_url,
                    'text'   => $c->text,
                    'time'   => $c->created_at->translatedFormat('d M Y, H:i'),
                ];
            })->values()->all();

            return [
                'id'        => $sub->id,
                'docTitle'  => $sub->title,
                'docType'   => $sub->type,
                'docStatus' => $sub->status,
                'docDate'   => $sub->submitted_at->translatedFormat('d M Y'),
                'resolved'  => (bool) $sub->resolved,
                'pdfUrl'    => $sub->file_path ? asset('storage/' . $sub->file_path) : null,
                'comments'  => $comments,
                'decision'  => $sub->decision ? [
                    'decision' => $sub->decision->decision,
                    'feedback' => $sub->decision->feedback,
                ] : null,
            ];
        })->values()->all();
    }
}
