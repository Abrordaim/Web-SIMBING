<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingStatusNotification;
use App\Mail\MeetingRescheduledNotification;
use App\Models\Meeting;
use Livewire\Component;

class ScheduleMeeting extends Component
{
    public bool $showEditModal   = false;
    public bool $showRejectModal = false;
    public ?int $selectedMeeting   = null;
    public ?int $rejectMeetingId   = null;
    public string $rejectReason    = '';

    // Form fields for editing
    public string $editTitle     = '';
    public string $editDate      = '';
    public string $editTimeStart = '';
    public string $editLocation  = '';
    public string $editType      = 'offline';
    public string $editNotes     = '';

    public function openEdit(int $id)
    {
        $meeting = Meeting::findOrFail($id);

        // Authorize: only student owner can edit
        Gate::authorize('cancel', $meeting);

        $this->selectedMeeting = $id;
        $this->editTitle      = $meeting->title;
        $this->editDate       = $meeting->date->format('Y-m-d');
        $this->editTimeStart  = $meeting->time_start;
        $this->editLocation   = $meeting->location;
        $this->editType       = $meeting->type;
        $this->editNotes      = $meeting->notes ?? '';
        $this->showEditModal  = true;
    }

    public function updateMeeting()
    {
        $this->validate([
            'editTitle'     => 'required|string|max:255',
            'editDate'      => 'required|date|after_or_equal:today',
            'editTimeStart' => 'required|date_format:H:i',
            'editLocation'  => 'required|string|max:255',
            'editType'      => 'required|in:online,offline',
        ], [
            'editTitle.required'       => 'Judul konsultasi wajib diisi.',
            'editDate.required'        => 'Tanggal wajib diisi.',
            'editDate.after_or_equal'  => 'Tanggal harus hari ini atau yang akan datang.',
            'editTimeStart.required'   => 'Waktu mulai wajib diisi.',
            'editTimeStart.date_format'=> 'Format waktu tidak valid.',
            'editLocation.required'    => 'Lokasi wajib diisi.',
            'editType.required'        => 'Tipe pertemuan wajib dipilih.',
        ]);

        $meeting = Meeting::findOrFail($this->selectedMeeting);

        Gate::authorize('cancel', $meeting);

        $meeting->update([
            'title'      => $this->editTitle,
            'date'       => $this->editDate,
            'time_start' => $this->editTimeStart,
            'location'   => $this->editLocation,
            'type'       => $this->editType,
            'notes'      => $this->editNotes ?: null,
            'status'     => 'pending', // reset to pending after edit
        ]);

        // Kirim email ke dosen tentang pengajuan ulang jadwal (queued)
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($lecturerUser->email)->queue(
                new MeetingRescheduledNotification($meeting, $studentUser, $lecturerUser)
            );
        }

        $this->closeEdit();
        session()->flash('success', 'Jadwal berhasil diperbarui dan dosen telah diberitahu.');
    }

    public function closeEdit()
    {
        $this->showEditModal  = false;
        $this->selectedMeeting = null;
        $this->editTitle      = '';
        $this->editDate       = '';
        $this->editTimeStart  = '';
        $this->editLocation   = '';
        $this->editType       = 'offline';
        $this->editNotes      = '';
        $this->resetValidation();
    }

    /**
     * Dosen: setujui pengajuan jadwal dari mahasiswa.
     */
    public function confirmMeeting(int $id)
    {
        $meeting = Meeting::findOrFail($id);

        // MeetingPolicy::confirm — cek role lecturer + ownership supervisi
        Gate::authorize('confirm', $meeting);

        $meeting->update(['status' => 'confirmed']);

        // Kirim email ke mahasiswa (queued)
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($studentUser->email)->queue(
                new MeetingStatusNotification($meeting, 'confirmed', $studentUser, $lecturerUser)
            );
        }
    }

    /**
     * Dosen: buka modal penolakan jadwal.
     */
    public function openReject(int $id)
    {
        $this->rejectMeetingId = $id;
        $this->rejectReason    = '';
        $this->showRejectModal = true;
        $this->resetValidation('rejectReason');
    }

    public function closeReject()
    {
        $this->showRejectModal = false;
        $this->rejectMeetingId = null;
        $this->rejectReason    = '';
        $this->resetValidation('rejectReason');
    }

    /**
     * Dosen: konfirmasi penolakan jadwal beserta alasan.
     */
    public function submitReject()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:500',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectReason.min'      => 'Alasan minimal 5 karakter.',
            'rejectReason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        $meeting = Meeting::findOrFail($this->rejectMeetingId);

        Gate::authorize('confirm', $meeting);

        $meeting->update([
            'status' => 'cancelled',
            'notes'  => $this->rejectReason,
        ]);

        // Kirim email ke mahasiswa dengan alasan penolakan (queued)
        $meeting->load('supervision.student.user', 'supervision.lecturer.user');
        $studentUser  = $meeting->supervision?->student?->user;
        $lecturerUser = $meeting->supervision?->lecturer?->user;
        if ($studentUser && $lecturerUser) {
            Mail::to($studentUser->email)->queue(
                new MeetingStatusNotification($meeting, 'cancelled', $studentUser, $lecturerUser, $this->rejectReason)
            );
        }

        $this->closeReject();
        session()->flash('success', 'Jadwal berhasil ditolak dan mahasiswa telah diberitahu.');
    }

    /**
     * Mahasiswa: batalkan pengajuan jadwal sendiri.
     */
    public function cancelMeeting(int $id)
    {
        $meeting = Meeting::findOrFail($id);

        // MeetingPolicy::cancel — cek role student + ownership supervisi
        Gate::authorize('cancel', $meeting);

        $meeting->update(['status' => 'cancelled']);
    }

    public function render()
    {
        $user = Auth::user();
        $isLecturer = $user && $user->isLecturer();

        // Get meetings based on role
        if ($user && $user->isStudent() && $user->student && $user->student->supervision) {
            $supervisionId = $user->student->supervision->id;
            $meetings = Meeting::where('supervision_id', $supervisionId)
                ->orderBy('date', 'desc')
                ->get();
            $lecturerName = $user->student->supervision->lecturer->user->name ?? '';
        } elseif ($user && $user->isLecturer() && $user->lecturer) {
            $supervisionIds = $user->lecturer->supervisions()->pluck('id');
            $meetings = Meeting::whereIn('supervision_id', $supervisionIds)
                ->with(['supervision.student.user'])
                ->orderBy('date', 'desc')
                ->get();
            $lecturerName = $user->name;
        } else {
            $meetings = collect();
            $lecturerName = '';
        }

        $allMeetings = $meetings->map(function ($m) use ($lecturerName, $user) {
            $displayName = $lecturerName;
            if ($user->isLecturer() && $m->supervision && $m->supervision->student) {
                $displayName = $m->supervision->student->user->name;
            }

            // $timeEnd = date('H:i', strtotime($m->time_start . ' +1 hour'));
            return [
                'id'       => $m->id,
                'title'    => $m->title,
                'lecturer' => $displayName,
                'date'     => $m->date->format('Y-m-d'),
                'time'     => $m->time_start,
                'location' => $m->location,
                'type'     => $m->type,
                'status'   => $m->status,
                'notes'    => $m->notes ?? '',
            ];
        })->values()->all();

        $upcomingMeetings = $isLecturer
            ? collect($allMeetings)->whereIn('status', ['confirmed', 'pending'])->values()->all()
            : collect($allMeetings)->whereIn('status', ['confirmed', 'pending', 'cancelled'])->values()->all();

        $pastMeetings = $isLecturer
            ? collect($allMeetings)->whereIn('status', ['completed', 'cancelled'])->values()->all()
            : collect($allMeetings)->whereIn('status', ['completed'])->values()->all();
        $editMeeting      = $this->selectedMeeting ? collect($allMeetings)->firstWhere('id', $this->selectedMeeting) : null;

        return view('livewire.schedule-meeting', compact('allMeetings', 'upcomingMeetings', 'pastMeetings', 'editMeeting', 'isLecturer'));
    }
}
