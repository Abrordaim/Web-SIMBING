<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewSubmissionNotification;
use App\Models\Meeting;
use App\Models\Submission;
use App\Models\TimelineEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class SubmissionController extends Controller
{
    /**
     * List submissions for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $statusMap = [
            'pending'  => ['label' => 'Menunggu Review', 'color' => 'blue'],
            'approved' => ['label' => 'Disetujui',       'color' => 'green'],
            'revision' => ['label' => 'Perlu Revisi',    'color' => 'yellow'],
            'rejected' => ['label' => 'Ditolak',         'color' => 'red'],
        ];

        if ($user->isStudent()) {
            $student = $user->student;
            if (! $student) {
                return response()->json(['success' => true, 'data' => ['submissions' => []]]);
            }

            $supervisionIds = $student->supervisions()->pluck('id');
            $submissions = Submission::whereIn('supervision_id', $supervisionIds)
                ->with(['supervision.lecturer.user', 'decision'])
                ->orderBy('submitted_at', 'desc')
                ->get();
        } elseif ($user->isLecturer()) {
            $lecturer = $user->lecturer;
            if (! $lecturer) {
                return response()->json(['success' => true, 'data' => ['submissions' => []]]);
            }

            $supervisionIds = $lecturer->supervisions()->pluck('id');
            $submissions = Submission::whereIn('supervision_id', $supervisionIds)
                ->with(['supervision.student.user', 'decision'])
                ->orderBy('submitted_at', 'desc')
                ->get();
        } else {
            $submissions = collect();
        }

        $formatted = $submissions->map(function ($sub) use ($statusMap, $user) {
            $s = $statusMap[$sub->status] ?? $statusMap['pending'];
            return [
                'id'           => $sub->id,
                'title'        => $sub->title,
                'chapter'      => $sub->chapter ?? $sub->type,
                'type'         => $sub->type,
                'description'  => $sub->description,
                'status'       => $sub->status,
                'statusLabel'  => $s['label'],
                'statusColor'  => $s['color'],
                'file_path'    => $sub->file_path ? asset('storage/' . $sub->file_path) : null,
                'file_size'    => $sub->file_size,
                'date'         => $sub->submitted_at->translatedFormat('d F Y'),
                'feedback'     => $sub->decision?->feedback,
                'lecturer'     => $user->isStudent()
                    ? ($sub->supervision->lecturer->user->name ?? '-')
                    : null,
                'student'      => $user->isLecturer()
                    ? ($sub->supervision->student->user->name ?? '-')
                    : null,
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data'    => ['submissions' => $formatted],
        ]);
    }

    /**
     * Create a new submission (student only).
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('submit-thesis');

        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Profil mahasiswa tidak ditemukan.'], 404);
        }

        $request->validate([
            'title'            => 'required|string|max:255',
            'chapter'          => 'nullable|string|max:100',
            'description'      => 'nullable|string|max:2000',
            'supervision_id'   => 'nullable|integer',
            'file'             => 'nullable|file|max:10240|mimes:pdf,doc,docx',
            'meeting_date'     => 'required|date|after_or_equal:today',
            'meeting_time'     => 'required|date_format:H:i',
            'meeting_type'     => 'required|in:online,offline',
        ]);

        // Get supervision
        $supervision = null;
        if ($request->supervision_id) {
            $supervision = $student->supervisions()->find($request->supervision_id);
        }
        if (! $supervision) {
            $supervision = $student->supervisions()->first();
        }
        if (! $supervision) {
            return response()->json(['success' => false, 'message' => 'Belum memiliki dosen pembimbing.'], 422);
        }

        // Determine type
        $type = 'Bab';
        if (str_contains(strtolower($request->title), 'revisi')) {
            $type = 'Revisi';
        } elseif (str_contains(strtolower($request->title), 'proposal')) {
            $type = 'Proposal';
        }

        // Store file
        $filePath = '/pdfs/placeholder.pdf';
        $fileSize = '0 KB';
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('pdfs', 'public');
            $fileSize = round($request->file('file')->getSize() / 1024 / 1024, 1) . ' MB';
        }

        // Create submission
        $submission = Submission::create([
            'supervision_id' => $supervision->id,
            'title'          => $request->title,
            'chapter'        => $request->chapter,
            'type'           => $type,
            'description'    => $request->description,
            'file_path'      => $filePath,
            'file_size'      => $fileSize,
            'status'         => 'pending',
            'submitted_at'   => now(),
        ]);

        // Create timeline event
        TimelineEvent::create([
            'supervision_id' => $supervision->id,
            'event'          => $request->title . ' dikirim, menunggu review',
            'type'           => 'pending',
            'event_date'     => now()->toDateString(),
        ]);

        // Create meeting
        $location = $request->meeting_type === 'online' ? 'Google Meet' : 'Ruang Dosen 301';
        Meeting::create([
            'supervision_id' => $supervision->id,
            'title'          => 'Bimbingan ' . ($request->chapter ?: $request->title),
            'date'           => $request->meeting_date,
            'time_start'     => $request->meeting_time,
            'location'       => $location,
            'type'           => $request->meeting_type,
            'status'         => 'pending',
            'notes'          => '',
        ]);

        // Email & Push notification to lecturer
        $lecturerUser = $supervision->lecturer?->user;
        if ($lecturerUser) {
            $submission->load('supervision.student.user');
            Mail::to($lecturerUser->email)->queue(
                new NewSubmissionNotification($submission, $lecturerUser, $user)
            );

            // Push Notification
            \App\Services\PushNotificationService::send(
                $lecturerUser->expo_push_token,
                'Pengajuan Bimbingan Baru',
                $user->name . ' telah mengirim pengajuan ' . $request->title,
                ['url' => '/(tabs)/dashboard']
            );
        }

        return response()->json([
            'success' => true,
            'data'    => ['submission_id' => $submission->id],
            'message' => 'Pengajuan bimbingan berhasil dikirim!',
        ], 201);
    }

    /**
     * Get a single submission with comments.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $submission = Submission::with(['comments.user', 'decision', 'supervision.student.user', 'supervision.lecturer.user'])
            ->findOrFail($id);

        // Authorization: user must be part of this supervision
        $user = $request->user();
        $supervision = $submission->supervision;
        if ($user->isStudent() && $supervision->student->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        if ($user->isLecturer() && $supervision->lecturer->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $comments = $submission->comments->map(function ($c) {
            return [
                'id'     => $c->id,
                'author' => $c->user->role === 'lecturer' ? 'lecturer' : 'student',
                'name'   => $c->user->name,
                'avatar' => $c->user->avatar_url,
                'text'   => $c->text,
                'time'   => $c->created_at->translatedFormat('d M Y, H:i'),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $submission->id,
                'title'      => $submission->title,
                'chapter'    => $submission->chapter,
                'type'       => $submission->type,
                'status'     => $submission->status,
                'description'=> $submission->description,
                'file_url'   => $submission->file_path ? asset('storage/' . $submission->file_path) : null,
                'file_size'  => $submission->file_size,
                'resolved'   => (bool) $submission->resolved,
                'date'       => $submission->submitted_at->translatedFormat('d F Y'),
                'feedback'   => $submission->decision?->feedback,
                'decision'   => $submission->decision?->decision,
                'student'    => $supervision->student->user->name,
                'lecturer'   => $supervision->lecturer->user->name,
                'comments'   => $comments,
            ],
        ]);
    }
}
