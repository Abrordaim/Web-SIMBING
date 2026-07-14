<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionDecisionNotification;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionDecision;
use App\Models\TimelineEvent;
use App\Models\ThesisSupervision;
use Livewire\Component;

class StudentDetailPage extends Component
{
    public string $studentId = '1';
    public string $activeTab = 'submissions';
    public ?int $expandedSubmission = null;
    public bool $showDecisionPanel = false;
    public ?int $selectedSubmission = null;
    public ?string $decision = null;
    public string $feedbackText = '';
    public ?string $successToast = null;

    public function mount(string $id = '1')
    {
        // Hanya dosen yang boleh mengakses halaman ini
        Gate::authorize('view-student-detail');

        $this->studentId = $id;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleSubmission(int $id)
    {
        $this->expandedSubmission = $this->expandedSubmission === $id ? null : $id;
    }

    public function openDecisionPanel(int $subId)
    {
        $this->selectedSubmission = $subId;
        $this->decision = null;
        $this->feedbackText = '';
        $this->showDecisionPanel = true;
    }

    public function closeDecisionPanel()
    {
        $this->showDecisionPanel = false;
        $this->selectedSubmission = null;
    }

    public function submitDecision()
    {
        if (!$this->decision || !$this->selectedSubmission) return;

        $submission = Submission::findOrFail($this->selectedSubmission);

        // Cek via Policy: hanya dosen pembimbing dari supervisi ini
        Gate::authorize('update', $submission);

        $user = Auth::user();

        // Create or update decision
        SubmissionDecision::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'lecturer_id' => $user->lecturer->id,
                'decision' => $this->decision,
                'feedback' => $this->feedbackText,
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
        $newStatus = $statusMap[$this->decision] ?? 'pending';
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
            'event' => $submission->title . ' ' . ($decisionLabels[$this->decision] ?? ''),
            'type' => $this->decision === 'approved' ? 'approved' : ($this->decision === 'rejected' ? 'revision' : 'revision'),
            'event_date' => now()->toDateString(),
        ]);

        $student = Student::find($this->studentId);
        $this->successToast = "Keputusan berhasil dikirim ke " . ($student ? $student->user->name : 'mahasiswa');

        // Simpan sebelum di-reset untuk email
        $decisionType    = $this->decision;
        $feedbackContent = $this->feedbackText;

        $this->showDecisionPanel = false;
        $this->decision = null;
        $this->feedbackText = '';
        $this->selectedSubmission = null;

        // Kirim email notifikasi ke mahasiswa (queued)
        $submission->load('supervision.student.user');
        $studentUser = $submission->supervision?->student?->user;
        if ($studentUser) {
            Mail::to($studentUser->email)->queue(
                new SubmissionDecisionNotification(
                    $submission,
                    $decisionType,
                    $feedbackContent,
                    $studentUser,
                    $user
                )
            );
        }

        $this->dispatch('clear-toast');
    }

    public function clearToast()
    {
        $this->successToast = null;
    }

    public function render()
    {
        $student = Student::with(['user'])->find($this->studentId);

        if (!$student) {
            $student = Student::with(['user'])->first();
            if (!$student) {
                abort(404);
            }
        }

        $supervision = ThesisSupervision::where('student_id', $student->id)
            ->with(['lecturer.user'])
            ->first();

        // Build student data array for the view
        $submissions = $supervision
            ? $supervision->submissions()->with(['decision'])->orderBy('submitted_at', 'desc')->get()
            : collect();

        $timeline = $supervision
            ? $supervision->timelineEvents()->orderBy('event_date', 'desc')->get()
            : collect();

        // Calculate progress dynamically: approved unique Bab/Proposal milestones out of 6
        $approvedCount = $submissions
            ->where('status', 'approved')
            ->whereIn('type', ['Bab', 'Proposal'])
            ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x'))
            ->unique()
            ->count();
        $calculatedProgress = min(100, (int) round($approvedCount / 6 * 100));

        $studentData = [
            'id' => $student->id,
            'name' => $student->user->name,
            'nim' => $student->nim,
            'email' => $student->user->email,
            'phone' => $student->user->phone ?? '',
            'semester' => $student->semester,
            'title' => $supervision ? $supervision->title : '',
            'progress' => $calculatedProgress,
            'status' => $supervision ? $supervision->status : 'active',
            'lastActivity' => $submissions->isNotEmpty()
                ? $submissions->first()->submitted_at->diffForHumans()
                : 'Belum ada aktivitas',
            'startDate' => $supervision ? $supervision->start_date->translatedFormat('F Y') : '',
            'submissions' => $submissions->map(function ($sub) {
                $decision = $sub->decision;
                return [
                    'id' => $sub->id,
                    'title' => $sub->title,
                    'type' => $sub->type,
                    'submittedAt' => $sub->submitted_at->translatedFormat('d F Y'),
                    'fileSize' => $sub->file_size ?? '0 KB',
                    'status' => $sub->status,
                    'feedback' => $decision ? $decision->feedback : null,
                ];
            })->values()->all(),
            'timeline' => $timeline->map(function ($t) {
                return [
                    'date' => $t->event_date->translatedFormat('d M Y'),
                    'event' => $t->event,
                    'type' => $t->type,
                ];
            })->values()->all(),
        ];

        $statusConfig = [
            'pending' => ['label' => 'Menunggu Review', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'approved' => ['label' => 'Disetujui', 'color' => 'bg-green-50 text-green-700 border-green-200'],
            'revision' => ['label' => 'Perlu Revisi', 'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-50 text-red-700 border-red-200'],
        ];

        $decisionOptions = [
            ['value' => 'approved', 'label' => 'Disetujui', 'description' => 'Dokumen diterima tanpa revisi', 'bg' => 'bg-green-50 border-green-300 text-green-800', 'activeBg' => 'bg-green-600 border-green-600 text-white'],
            ['value' => 'revision_minor', 'label' => 'Revisi Minor', 'description' => 'Perlu perbaikan kecil', 'bg' => 'bg-yellow-50 border-yellow-300 text-yellow-800', 'activeBg' => 'bg-yellow-500 border-yellow-500 text-white'],
            ['value' => 'revision_major', 'label' => 'Revisi Mayor', 'description' => 'Perlu perbaikan signifikan', 'bg' => 'bg-orange-50 border-orange-300 text-orange-800', 'activeBg' => 'bg-orange-500 border-orange-500 text-white'],
            ['value' => 'rejected', 'label' => 'Tidak Disetujui', 'description' => 'Dokumen ditolak', 'bg' => 'bg-red-50 border-red-300 text-red-800', 'activeBg' => 'bg-red-600 border-red-600 text-white'],
        ];

        $decisionColors = [
            'approved' => 'text-green-700 bg-green-50 border-green-200',
            'revision_minor' => 'text-yellow-700 bg-yellow-50 border-yellow-200',
            'revision_major' => 'text-orange-700 bg-orange-50 border-orange-200',
            'rejected' => 'text-red-700 bg-red-50 border-red-200',
        ];

        $decisionLabels = [
            'approved' => 'Disetujui',
            'revision_minor' => 'Revisi Minor',
            'revision_major' => 'Revisi Mayor',
            'rejected' => 'Tidak Disetujui',
        ];

        // Use submittedDecisions from actual DB
        $submittedDecisions = [];
        foreach ($submissions as $sub) {
            if ($sub->decision) {
                $submittedDecisions[$sub->id] = [
                    'decision' => $sub->decision->decision,
                    'feedback' => $sub->decision->feedback,
                ];
            }
        }
        $this->submittedDecisions = $submittedDecisions;

        return view('livewire.student-detail-page', [
            'student' => $studentData,
            'statusConfig' => $statusConfig,
            'decisionOptions' => $decisionOptions,
            'decisionColors' => $decisionColors,
            'decisionLabels' => $decisionLabels,
        ]);
    }

    public array $submittedDecisions = [];
}
