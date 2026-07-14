<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Submission;
use App\Models\Comment;
use App\Models\ThesisSupervision;
use Livewire\Component;

class RevisionFeedback extends Component
{
    public string $role = 'student';
    public ?int $selectedStudentId = null;
    public string $search = '';
    public string $filter = 'all';
    public array $replyTexts = [];
    public ?int $selectedBabId = null;
    public array $expandedThreads = [];

    public function mount()
    {
        $user = Auth::user();
        $this->role = $user ? $user->role : session('role', 'student');

        if ($this->role === 'lecturer') {
            $this->selectedStudentId = null;
            $this->selectedBabId = null;
        } else {
            // Student: select first submission
            if ($user && $user->student && $user->student->supervision) {
                $firstSub = $user->student->supervision->submissions()->orderBy('submitted_at', 'desc')->first();
                $this->selectedBabId = $firstSub ? $firstSub->id : null;
            }
        }
    }

    public function selectStudent(int $id)
    {
        $this->selectedStudentId = $id;
        // Select first submission of this student
        $supervision = ThesisSupervision::where('student_id', $id)->first();
        if ($supervision) {
            $firstSub = $supervision->submissions()->orderBy('submitted_at', 'desc')->first();
            $this->selectedBabId = $firstSub ? $firstSub->id : null;
        } else {
            $this->selectedBabId = null;
        }
        $this->expandedThreads = [];
        $this->filter = 'all';
    }

    public function backToStudentList()
    {
        $this->selectedStudentId = null;
        $this->selectedBabId = null;
        $this->expandedThreads = [];
        $this->filter = 'all';
    }

    public function selectBab(int $babId)
    {
        $this->selectedBabId = $babId;
    }

    public function toggleThread(int $threadId)
    {
        if (in_array($threadId, $this->expandedThreads)) {
            $this->expandedThreads = array_values(array_diff($this->expandedThreads, [$threadId]));
        } else {
            $this->expandedThreads[] = $threadId;
        }
    }

    public function sendReply(int $threadId, int $studentId)
    {
        Gate::authorize('send-comment');

        $key = $threadId;
        $text = trim($this->replyTexts[$key] ?? '');
        if (empty($text)) return;

        $user = Auth::user();
        if (!$user) return;

        Comment::create([
            'submission_id' => $threadId,
            'user_id'       => $user->id,
            'text'          => $text,
        ]);

        $this->replyTexts[$key] = '';
    }

    public function resolveThread(int $threadId, int $studentId)
    {
        $submission = Submission::findOrFail($threadId);

        // SubmissionPolicy::resolve — hanya dosen pembimbing mahasiswa tsb
        Gate::authorize('resolve', $submission);

        $submission->update(['resolved' => !$submission->resolved]);
    }

    public function render()
    {
        $user = Auth::user();

        // Build student list for lecturer
        $students = [];
        $filteredStudents = [];
        $selectedStudent = null;
        

        if ($this->role === 'lecturer' && $user && $user->lecturer) {
            $supervisions = $user->lecturer->supervisions()->with(['student.user'])->get();

            $students = $supervisions->map(function ($sup) {
                $submissions = $sup->submissions;
                $latestComment = Comment::whereIn('submission_id', $submissions->pluck('id'))
                    ->latest()
                    ->first();

                $unreadCount = $submissions->where('status', 'pending')->count();

                return [
                    'id' => $sup->student->id,
                    'name' => $sup->student->user->name,
                    'nim' => $sup->student->nim,
                    'avatar' => $sup->student->user?->avatar_url,
                    'title' => $sup->title,
                    'unread' => $unreadCount,
                    'lastMsg' => $latestComment ? \Str::limit($latestComment->text, 40) : 'Belum ada pesan',
                    'lastTime' => $latestComment ? $latestComment->created_at->diffForHumans(short: true) : '',
                    'status' => $sup->status === 'warning' ? 'warning' : 'active',
                    'totalDocs' => $submissions->count(),
                    'pendingCount' => $submissions->where('status', 'pending')->count(),
                    'revisionCount' => $submissions->where('status', 'revision')->count(),
                    'approvedCount' => $submissions->where('status', 'approved')->count(),
                ];
            })->values()->all();

            $filteredStudents = collect($students)->filter(function ($s) {
                if (empty($this->search)) return true;
                return str_contains(strtolower($s['name']), strtolower($this->search))
                    || str_contains($s['nim'], $this->search);
            })->values()->all();

            if ($this->selectedStudentId) {
                $selectedStudent = collect($students)->firstWhere('id', $this->selectedStudentId);
                // dd($selectedStudent); 
            }
        }

        // Get submissions (threads) for the relevant student
        $currentThreads = [];
        if ($this->role === 'student' && $user && $user->student && $user->student->supervision) {
            $supervision = $user->student->supervision;
            $submissions = $supervision->submissions()->with(['comments.user'])->orderBy('submitted_at', 'desc')->get();
            $currentThreads = $this->mapSubmissionsToThreads($submissions, $user);
        } elseif ($this->role === 'lecturer' && $this->selectedStudentId) {
            $supervision = ThesisSupervision::where('student_id', $this->selectedStudentId)->first();
            if ($supervision) {
                $submissions = $supervision->submissions()->with(['comments.user'])->orderBy('submitted_at', 'desc')->get();
                $currentThreads = $this->mapSubmissionsToThreads($submissions, $user);
            }
        }

        // Apply filter
        $filteredThreads = collect($currentThreads)->filter(function ($t) {
            if ($this->filter === 'open') return !$t['resolved'];
            if ($this->filter === 'resolved') return $t['resolved'];
            return true;
        })->values()->all();

        $statusCfg = [
            'pending' => ['label' => 'Menunggu Review', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'approved' => ['label' => 'Disetujui', 'cls' => 'bg-green-50 text-green-700 border-green-200'],
            'revision' => ['label' => 'Perlu Revisi', 'cls' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
            'rejected' => ['label' => 'Ditolak', 'cls' => 'bg-red-50 text-red-700 border-red-200'],
        ];

        // Selected bab's PDF
        $selectedPdfUrl = null;
        $selectedThread = null;
        foreach ($currentThreads as $t) {
            if ($t['id'] === $this->selectedBabId) {
                $selectedPdfUrl = $t['pdfUrl'] ?? null;
                $selectedThread = $t;
                break;
            }
        }


        $studentId = $this->selectedStudentId;

        return view('livewire.revision-feedback', compact(
            'students', 'filteredStudents', 'selectedStudent', 'filteredThreads',
            'statusCfg', 'studentId', 'selectedPdfUrl', 'selectedThread'
        ));
    }

    private function mapSubmissionsToThreads($submissions, $user): array
    {
        return $submissions->map(function ($sub) use ($user) {
            $comments = $sub->comments->map(function ($c) {
                return [
                    'id' => $c->id,
                    'author' => $c->user->role === 'lecturer' ? 'lecturer' : 'student',
                    'name' => $c->user->name,
                    'avatar' => $c->user->avatar_url,
                    'text' => $c->text,
                    'time' => $c->created_at->translatedFormat('d M Y, H:i'),
                ];
            })->values()->all();

            return [
                'id' => $sub->id,
                'docTitle' => $sub->title,
                'docType' => $sub->type,
                'docStatus' => $sub->status,
                'docDate' => $sub->submitted_at->translatedFormat('d M Y'),
                'resolved' => (bool) $sub->resolved,
                'pdfUrl' => $sub->file_path ? asset('storage/' . $sub->file_path) : null,
                'comments' => $comments,
            ];
        })->values()->all();
    }
}
