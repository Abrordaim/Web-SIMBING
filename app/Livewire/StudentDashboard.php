<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\ThesisSupervision;
use Livewire\Component;

class StudentDashboard extends Component
{
    public bool $showAllSchedules = false;
    public int $calendarMonth;
    public int $calendarYear;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            session(['role' => $user->role]);
        }

        // Initialize calendar to current month (0-indexed for JS compat)
        $this->calendarMonth = (int) now()->format('n') - 1;
        $this->calendarYear = (int) now()->format('Y');
    }

    public function toggleSchedules()
    {
        $this->showAllSchedules = !$this->showAllSchedules;
    }

    public function previousMonth()
    {
        if ($this->calendarMonth === 0) {
            $this->calendarMonth = 11;
            $this->calendarYear--;
        } else {
            $this->calendarMonth--;
        }
    }

    public function nextMonth()
    {
        if ($this->calendarMonth === 11) {
            $this->calendarMonth = 0;
            $this->calendarYear++;
        } else {
            $this->calendarMonth++;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $student = $user ? $user->student : null;
        $supervision = $student ? $student->supervision : null;

        // Stats from DB
        $submissions = $supervision ? $supervision->submissions()->orderBy('submitted_at', 'desc')->get() : collect();
        $meetings = $supervision ? $supervision->meetings()->orderBy('date', 'desc')->get() : collect();

        $stats = [
            ['label' => 'Total Bimbingan', 'value' => (string) $submissions->count(), 'color' => 'blue', 'icon' => 'file-text'],
            ['label' => 'Menunggu Feedback', 'value' => (string) $submissions->where('status', 'pending')->count(), 'color' => 'yellow', 'icon' => 'clock'],
            ['label' => 'Selesai', 'value' => (string) $submissions->where('status', 'approved')->count(), 'color' => 'green', 'icon' => 'check-circle'],
            ['label' => 'Revisi', 'value' => (string) $submissions->where('status', 'revision')->count(), 'color' => 'red', 'icon' => 'alert-circle'],
        ];

        // Recent activities from submissions
        $statusMap = [
            'approved' => ['label' => 'Disetujui', 'type' => 'success'],
            'revision' => ['label' => 'Perlu Revisi', 'type' => 'warning'],
            'pending' => ['label' => 'Menunggu Review', 'type' => 'pending'],
            'rejected' => ['label' => 'Ditolak', 'type' => 'error'],
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

        // ── Thesis progress: count unique approved BAB/Proposal milestones ──────
        // Formula: (approved unique milestones / 6) * 100
        // 6 = 1 Proposal + 5 BAB — standard Indonesian thesis structure
        $totalMilestones = 6;
        $approvedMilestoneCount = $submissions
            ->where('status', 'approved')
            ->whereIn('type', ['Bab', 'Proposal'])
            ->map(fn($s) => $s->type . '-' . ($s->chapter ?? 'x')) // unique key per chapter
            ->unique()
            ->count();
        $thesisProgress = min(100, (int) round($approvedMilestoneCount / $totalMilestones * 100));

        // ── Thesis title & supervisor(s) ─────────────────────────────────────────
        $thesisTitle = $supervision?->title ?? 'Belum ditentukan';

        // Support multiple supervisors
        $supervisorNames = 'Belum ditentukan';
        if ($student) {
            $names = $student->supervisions
                ->map(fn($sv) => $sv->lecturer?->user?->name)
                ->filter()
                ->values();
            if ($names->isNotEmpty()) {
                $supervisorNames = $names->join(', ');
            }
        }

        $studentName = $user?->name ?? 'Mahasiswa';

        // Meetings
        $allMeetings = $meetings->map(function ($m) use ($supervision) {
            // $timeEnd = date('H:i', strtotime($m->time_start . ' +1 hour'));
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

        // Calendar data
        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->calendarMonth + 1, $this->calendarYear);
        $firstDay = date('w', mktime(0, 0, 0, $this->calendarMonth + 1, 1, $this->calendarYear));

        return view('livewire.student-dashboard', [
            'stats'            => $stats,
            'recentActivities' => $recentActivities,
            'allMeetings'      => $allMeetings,
            'thisWeekMeetings' => $thisWeekMeetings,
            'monthNames'       => $monthNames,
            'daysInMonth'      => $daysInMonth,
            'firstDay'         => $firstDay,
            'thesisTitle'      => $thesisTitle,
            'supervisorNames'  => $supervisorNames,
            'thesisProgress'   => $thesisProgress,
            'studentName'      => $studentName,
        ]);
    }
}
