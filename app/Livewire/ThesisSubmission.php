<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewSubmissionNotification;
use App\Models\Submission;
use App\Models\Meeting;
use App\Models\TimelineEvent;
use Livewire\Component;
use Livewire\WithFileUploads;

class ThesisSubmission extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $chapter = '';
    public string $description = '';
    public string $meetingDate = '';
    public string $meetingTime = '';
    public string $meetingType = 'offline';
    public $selectedFile;
    public string $selectedSupervisionId = '';

    public function submit()
    {
        Gate::authorize('submit-thesis');

        $user = Auth::user();
        if (!$user || !$user->student) return;

        $student = $user->student;

        // Get the selected supervision
        $supervision = null;
        if ($this->selectedSupervisionId) {
            $supervision = $student->supervisions()->find($this->selectedSupervisionId);
        }

        // Fallback to first supervision if none selected
        if (!$supervision) {
            $supervision = $student->supervisions()->first();
        }

        if (!$supervision) return;

        // Determine submission type
        $type = 'Bab';
        if (str_contains(strtolower($this->title), 'revisi')) {
            $type = 'Revisi';
        } elseif (str_contains(strtolower($this->title), 'proposal')) {
            $type = 'Proposal';
        }

        // Store file
        $filePath = '/pdfs/placeholder.pdf';
        $fileSize = '0 KB';
        if ($this->selectedFile) {
            $filePath = $this->selectedFile->store('pdfs', 'public');
            $fileSize = round($this->selectedFile->getSize() / 1024 / 1024, 1) . ' MB';
        }

        // Create submission
        $submission = Submission::create([
            'supervision_id' => $supervision->id,
            'title' => $this->title,
            'chapter' => $this->chapter ?: null,
            'type' => $type,
            'description' => $this->description ?: null,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Create timeline event
        TimelineEvent::create([
            'supervision_id' => $supervision->id,
            'event' => $this->title . ' dikirim, menunggu review',
            'type' => 'pending',
            'event_date' => now()->toDateString(),
        ]);

        // Create meeting (wajib)
        $location = $this->meetingType === 'online' ? 'Google Meet' : 'Ruang Dosen 301';
        Meeting::create([
            'supervision_id' => $supervision->id,
            'title'          => 'Bimbingan ' . ($this->chapter ?: $this->title),
            'date'           => $this->meetingDate,
            'time_start'     => $this->meetingTime,
            'location'       => $location,
            'type'           => $this->meetingType,
            'status'         => 'pending',
            'notes'          => '',
        ]);

        session()->flash('message', 'Pengajuan bimbingan berhasil dikirim!');

        // Kirim email notifikasi ke dosen (queued)
        $lecturerUser = $supervision->lecturer?->user;
        if ($lecturerUser) {
            $submission->load('supervision.student.user');
            Mail::to($lecturerUser->email)->queue(
                new NewSubmissionNotification($submission, $lecturerUser, $user)
            );
        }

        $this->reset(['title', 'chapter', 'description', 'meetingDate', 'meetingTime', 'selectedFile', 'selectedSupervisionId']);
        $this->meetingType = 'offline';
    }

    public function removeFile()
    {
        $this->selectedFile = null;
    }

    public function render()
    {
        $user = Auth::user();
        $student = $user ? $user->student : null;

        // Get all supervisions for this student (to populate the supervisor dropdown)
        $supervisions = $student ? $student->supervisions()->with('lecturer.user')->get() : collect();

        $supervisorOptions = $supervisions->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->lecturer->user->name,
                'department' => $s->lecturer->department,
            ];
        })->values()->all();

        // Get all submissions across all supervisions
        $previousSubmissions = [];
        if ($supervisions->isNotEmpty()) {
            $statusMap = [
                'pending' => ['label' => 'Menunggu Review', 'color' => 'blue'],
                'approved' => ['label' => 'Disetujui', 'color' => 'green'],
                'revision' => ['label' => 'Perlu Revisi', 'color' => 'yellow'],
                'rejected' => ['label' => 'Ditolak', 'color' => 'red'],
            ];

            $previousSubmissions = Submission::whereIn('supervision_id', $supervisions->pluck('id'))
                ->orderBy('submitted_at', 'desc')
                ->get()
                ->map(function ($sub) use ($statusMap) {
                    $s = $statusMap[$sub->status] ?? $statusMap['pending'];
                    return [
                        'id' => $sub->id,
                        'title' => $sub->title,
                        'chapter' => $sub->chapter ?? $sub->type,
                        'date' => $sub->submitted_at->translatedFormat('d F Y'),
                        'status' => $s['label'],
                        'statusColor' => $s['color'],
                        'lecturer' => $sub->supervision->lecturer->user->name ?? '-',
                    ];
                })->values()->all();
        }

        return view('livewire.thesis-submission', compact('previousSubmissions', 'supervisorOptions'));
    }
}
