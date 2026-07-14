<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MeetingRescheduledNotification;
use App\Mail\MeetingStatusNotification;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{
    /**
     * List meetings for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStudent() && $user->student && $user->student->supervision) {
            $supervisionId = $user->student->supervision->id;
            $meetings = Meeting::where('supervision_id', $supervisionId)
                ->orderBy('date', 'desc')
                ->get();
            $lecturerName = $user->student->supervision->lecturer->user->name ?? '';
        } elseif ($user->isLecturer() && $user->lecturer) {
            $supervisionIds = $user->lecturer->supervisions()->pluck('id');
            $meetings = Meeting::whereIn('supervision_id', $supervisionIds)
                ->with(['supervision.student.user'])
                ->orderBy('date', 'desc')
                ->get();
            $lecturerName = $user->name;
        } else {
            return response()->json(['success' => true, 'data' => ['meetings' => []]]);
        }

        $allMeetings = $meetings->map(function ($m) use ($lecturerName, $user) {
            $displayName = $lecturerName;
            if ($user->isLecturer() && $m->supervision && $m->supervision->student) {
                $displayName = $m->supervision->student->user->name;
            }

            return [
                'id'       => $m->id,
                'title'    => $m->title,
                'person'   => $displayName,
                'date'     => $m->date->format('Y-m-d'),
                'time'     => $m->time_start,
                'location' => $m->location,
                'type'     => $m->type,
                'status'   => $m->status,
                'notes'    => $m->notes ?? '',
            ];
        })->values()->all();

        $isLecturer = $user->isLecturer();

        $upcoming = collect($allMeetings)->filter(function ($m) use ($isLecturer) {
            return $isLecturer
                ? in_array($m['status'], ['confirmed', 'pending'])
                : in_array($m['status'], ['confirmed', 'pending', 'cancelled']);
        })->values()->all();

        $past = collect($allMeetings)->filter(function ($m) use ($isLecturer) {
            return $isLecturer
                ? in_array($m['status'], ['completed', 'cancelled'])
                : in_array($m['status'], ['completed']);
        })->values()->all();

        return response()->json([
            'success' => true,
            'data'    => [
                'upcoming' => $upcoming,
                'past'     => $past,
            ],
        ]);
    }

    /**
     * Update a meeting (student only — reschedule).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $meeting = Meeting::findOrFail($id);
        Gate::authorize('cancel', $meeting);

        $request->validate([
            'title'      => 'required|string|max:255',
            'date'       => 'required|date|after_or_equal:today',
            'time_start' => 'required|date_format:H:i',
            'location'   => 'required|string|max:255',
            'type'       => 'required|in:online,offline',
            'notes'      => 'nullable|string|max:500',
        ]);

        $meeting->update([
            'title'      => $request->title,
            'date'       => $request->date,
            'time_start' => $request->time_start,
            'location'   => $request->location,
            'type'       => $request->type,
            'notes'      => $request->notes,
            'status'     => 'pending',
        ]);

        // Notify lecturer
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($lecturerUser->email)->queue(
                new MeetingRescheduledNotification($meeting, $studentUser, $lecturerUser)
            );

            \App\Services\PushNotificationService::send(
                $lecturerUser->expo_push_token,
                'Jadwal Bimbingan Diubah',
                $studentUser->name . ' mengajukan perubahan jadwal.',
                ['url' => '/(tabs)/schedule']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui.',
        ]);
    }

    /**
     * Lecturer: confirm a meeting.
     */
    public function confirm(Request $request, int $id): JsonResponse
    {
        $meeting = Meeting::findOrFail($id);
        Gate::authorize('confirm', $meeting);

        $meeting->update(['status' => 'confirmed']);

        // Notify student
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($studentUser->email)->queue(
                new MeetingStatusNotification($meeting, 'confirmed', $studentUser, $lecturerUser)
            );

            \App\Services\PushNotificationService::send(
                $studentUser->expo_push_token,
                'Jadwal Dikonfirmasi',
                $lecturerUser->name . ' telah menyetujui jadwal bimbingan.',
                ['url' => '/(tabs)/schedule']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dikonfirmasi.',
        ]);
    }

    /**
     * Lecturer: reject a meeting with reason.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $meeting = Meeting::findOrFail($id);
        Gate::authorize('confirm', $meeting);

        $request->validate([
            'reason' => 'required|string|min:5|max:500',
        ]);

        $meeting->update([
            'status' => 'cancelled',
            'notes'  => $request->reason,
        ]);

        // Notify student
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($studentUser->email)->queue(
                new MeetingStatusNotification($meeting, 'cancelled', $studentUser, $lecturerUser, $request->reason)
            );

            \App\Services\PushNotificationService::send(
                $studentUser->expo_push_token,
                'Jadwal Ditolak',
                $lecturerUser->name . ' membatalkan jadwal bimbingan.',
                ['url' => '/(tabs)/schedule']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditolak.',
        ]);
    }

    /**
     * Student: cancel own meeting request.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $meeting = Meeting::findOrFail($id);
        Gate::authorize('cancel', $meeting);

        $meeting->update(['status' => 'cancelled']);

        $meeting->load('supervision.lecturer.user', 'supervision.student.user');
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        $studentUser  = $meeting->supervision?->student?->user;

        if ($lecturerUser && $studentUser) {
            \App\Services\PushNotificationService::send(
                $lecturerUser->expo_push_token,
                'Jadwal Dibatalkan',
                $studentUser->name . ' telah membatalkan jadwal bimbingan.',
                ['url' => '/(tabs)/schedule']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibatalkan.',
        ]);
    }
}
