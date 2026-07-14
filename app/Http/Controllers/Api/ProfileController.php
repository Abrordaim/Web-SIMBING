<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get current user profile with full details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'avatar_url' => $user->avatar_url,
            'initials'   => $user->initials,
        ];

        if ($user->isStudent() && $user->student) {
            $student = $user->student;
            $supervision = $student->supervision;

            $supervisors = $student->supervisions()
                ->with('lecturer.user')
                ->get()
                ->map(fn($sv) => $sv->lecturer?->user?->name)
                ->filter()
                ->values()
                ->all();

            $data['student'] = [
                'nim'        => $student->nim,
                'semester'   => $student->semester,
                'department' => $student->department,
                'faculty'    => $student->faculty,
            ];

            $data['thesis'] = [
                'title'       => $supervision?->title ?? 'Belum ditentukan',
                'supervisors' => $supervisors,
                'start_date'  => $supervision?->start_date?->translatedFormat('d F Y') ?? '-',
                'progress'    => $supervision?->progress ?? 0,
            ];

            // Stats
            $submissions = $supervision ? $supervision->submissions : collect();
            $meetings = $supervision ? $supervision->meetings : collect();
            $data['stats'] = [
                ['label' => 'Total Bimbingan',     'value' => (string) $submissions->count()],
                ['label' => 'Dokumen Disetujui',   'value' => (string) $submissions->where('status', 'approved')->count()],
                ['label' => 'Jadwal Konsultasi',   'value' => (string) $meetings->count()],
                ['label' => 'Revisi Selesai',      'value' => (string) $submissions->where('resolved', true)->count()],
            ];
        }

        if ($user->isLecturer() && $user->lecturer) {
            $lecturer = $user->lecturer;

            $data['lecturer'] = [
                'nidn'           => $lecturer->nidn,
                'department'     => $lecturer->department,
                'faculty'        => $lecturer->faculty,
                'specialization' => $lecturer->specialization,
            ];

            // Stats
            $supervisions = $lecturer->supervisions;
            $totalStudents = $supervisions->count();
            $totalSubmissions = 0;
            $totalMeetings = 0;
            foreach ($supervisions as $sup) {
                $totalSubmissions += $sup->submissions->count();
                $totalMeetings += $sup->meetings->count();
            }

            $data['stats'] = [
                ['label' => 'Mahasiswa Bimbingan', 'value' => (string) $totalStudents],
                ['label' => 'Total Dokumen',       'value' => (string) $totalSubmissions],
                ['label' => 'Jadwal Konsultasi',   'value' => (string) $totalMeetings],
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Update user profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'phone']));

        if ($user->isStudent() && $user->student) {
            $request->validate([
                'nim'        => 'sometimes|string|max:20',
                'semester'   => 'sometimes|nullable|integer|min:1|max:14',
                'department' => 'sometimes|string|max:255',
                'faculty'    => 'sometimes|string|max:255',
            ]);

            $user->student->update(
                $request->only(['nim', 'semester', 'department', 'faculty'])
            );
        }

        if ($user->isLecturer() && $user->lecturer) {
            $request->validate([
                'nidn'           => 'sometimes|string|max:20',
                'department'     => 'sometimes|string|max:255',
                'faculty'        => 'sometimes|string|max:255',
                'specialization' => 'sometimes|nullable|string|max:255',
            ]);

            $user->lecturer->update(
                $request->only(['nidn', 'department', 'faculty', 'specialization'])
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }

    /**
     * Save Expo push token.
     */
    public function savePushToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();
        $user->update(['expo_push_token' => $request->token]);

        return response()->json([
            'success' => true,
            'message' => 'Push token saved.',
        ]);
    }
}
