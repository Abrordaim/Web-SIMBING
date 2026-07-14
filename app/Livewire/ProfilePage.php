<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\ThesisSupervision;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfilePage extends Component
{
    use WithFileUploads;

    public bool $isEditing = false;
    public string $name = '';
    public string $nim = '';
    public string $email = '';
    public string $phone = '';
    public string $department = '';
    public string $faculty = '';
    public string $role = '';
    public string $semester = '1';
    public string $thesisTitle = '';
    public string $startDate = '';
    public ?string $avatarUrl = null;
    public $newAvatar = null;

    // Multi-select supervisor
    public array $selectedSupervisorIds = [];

    public function mount()
    {
        $user = Auth::user();
        if (!$user)
            return;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->avatarUrl = $user->avatar_url;
        $this->role = $user->role;

        if ($user->isStudent()) {
            $student = $user->student;
            if ($student) {
                $this->nim = $student->nim ?? '';
                $this->department = $student->department ?? '';
                $this->faculty = $student->faculty ?? '';
                $this->semester = (string) ($student->semester ?? 1);

                // Load all supervisions (multiple supervisors)
                $supervisions = $student->supervisions;
                if ($supervisions->isNotEmpty()) {
                    $this->selectedSupervisorIds = $supervisions->pluck('lecturer_id')->toArray();
                    // Use first supervision for thesis title & start date
                    $first = $supervisions->first();
                    $this->thesisTitle = $first->title;
                    $this->startDate = $first->start_date->translatedFormat('F Y');
                }
            }
        } elseif ($user->isLecturer()) {
            $lecturer = $user->lecturer;
            if ($lecturer) {
                $this->nim = $lecturer->nidn ?? '';
                $this->department = $lecturer->department ?? '';
                $this->faculty = $lecturer->faculty ?? '';
            }
        }
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;

        // If cancelling, reload original data
        if (!$this->isEditing) {
            $this->newAvatar = null;
            $this->mount();
        }
    }

    public function removeAvatar()
    {
        $user = \App\Models\User::find(Auth::id());
        if ($user && $user->avatar_url) {
            // Only delete local files, not external Google URLs
            if (!str_starts_with($user->avatar_url, 'http')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $user->avatar_url = null;
            $user->save();
            $this->avatarUrl = null;
        }
        $this->newAvatar = null;
    }

    public function toggleSupervisor($lecturerId)
    {
        $lecturerId = (int) $lecturerId;
        if (in_array($lecturerId, $this->selectedSupervisorIds)) {
            $this->selectedSupervisorIds = array_values(
                array_filter($this->selectedSupervisorIds, fn($id) => $id !== $lecturerId)
            );
        } else {
            $this->selectedSupervisorIds[] = $lecturerId;
        }
    }

    public function save()
    {
        $user = \App\Models\User::find(Auth::id());
        if (!$user) return;

        // Handle avatar upload
        if ($this->newAvatar) {
            $this->validate([
                'newAvatar' => 'image|max:2048', // 2MB max
            ]);

            // Delete old local avatar if exists (skip external URLs)
            if ($user->avatar_url && !str_starts_with($user->avatar_url, 'http')) {
                $oldPath = str_replace('/storage/', '', $user->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $this->newAvatar->store('avatars', 'public');
            $this->avatarUrl = '/storage/' . $path;
        }

        // Update user info with explicit save
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->avatar_url = $this->avatarUrl;
        $user->save();

        if ($user->isStudent()) {
            // Use firstOrCreate so profile editing works even if record was never created
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                ['nim' => null, 'semester' => 1, 'department' => null, 'faculty' => null]
            );

            // Update student academic info
            $student->update([
                'nim'        => $this->nim ?: null,
                'semester'   => (int) $this->semester ?: 1,
                'department' => $this->department ?: null,
                'faculty'    => $this->faculty ?: null,
            ]);

            // Sync supervisors: remove unselected, add new ones
            $existingSupervisions = $student->supervisions;
            $existingLecturerIds = $existingSupervisions->pluck('lecturer_id')->toArray();

            // Remove supervisions for deselected lecturers
            $toRemove = array_diff($existingLecturerIds, $this->selectedSupervisorIds);
            if (!empty($toRemove)) {
                ThesisSupervision::where('student_id', $student->id)
                    ->whereIn('lecturer_id', $toRemove)
                    ->delete();
            }

            // Add new supervisions for newly selected lecturers
            $toAdd = array_diff($this->selectedSupervisorIds, $existingLecturerIds);
            foreach ($toAdd as $lecturerId) {
                ThesisSupervision::create([
                    'student_id'  => $student->id,
                    'lecturer_id' => $lecturerId,
                    'title'       => $this->thesisTitle ?: 'Belum ditentukan',
                    'progress'    => 0,
                    'status'      => 'active',
                    'start_date'  => now(),
                ]);
            }

            // Update thesis title on all existing supervisions
            if ($this->thesisTitle) {
                ThesisSupervision::where('student_id', $student->id)->update([
                    'title' => $this->thesisTitle,
                ]);
            }
        } elseif ($user->isLecturer()) {
            // Use firstOrCreate so profile editing works even if record was never created
            $lecturer = Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['nidn' => null, 'department' => null, 'faculty' => null]
            );

            $lecturer->update([
                'nidn'       => $this->nim ?: null,
                'department' => $this->department ?: null,
                'faculty'    => $this->faculty ?: null,
            ]);
        }

        $this->newAvatar = null;
        $this->isEditing = false;
    }

    public function render()
    {
        $user = Auth::user();

        // Available lecturers for the multi-select
        $availableLecturers = Lecturer::with('user')->get()->map(function ($l) {
            return [
                'id' => $l->id,
                'name' => $l->user->name,
                'department' => $l->department,
            ];
        });

        if ($user && $user->isStudent() && $user->student) {
            $supervisions = $user->student->supervisions;
            $allSubmissions = \App\Models\Submission::whereIn('supervision_id', $supervisions->pluck('id'))->get();

            $stats = [
                ['label' => 'Total Bimbingan', 'value' => (string) $allSubmissions->count()],
                ['label' => 'Bimbingan Disetujui', 'value' => (string) $allSubmissions->where('status', 'approved')->count()],
                ['label' => 'Rata-rata Waktu Review', 'value' => '2 hari'],
                ['label' => 'Progress Skripsi', 'value' => ($supervisions->isNotEmpty() ? $supervisions->first()->progress : 0) . '%'],
            ];

            // Supervisor names for display
            $supervisorNames = $supervisions->map(fn($s) => $s->lecturer->user->name)->toArray();
        } elseif ($user && $user->isLecturer() && $user->lecturer) {
            $supervisions = $user->lecturer->supervisions;
            $allSubmissions = \App\Models\Submission::whereIn('supervision_id', $supervisions->pluck('id'))->get();

            $stats = [
                ['label' => 'Total Mahasiswa', 'value' => (string) $supervisions->count()],
                ['label' => 'Total Submissions', 'value' => (string) $allSubmissions->count()],
                ['label' => 'Menunggu Review', 'value' => (string) $allSubmissions->where('status', 'pending')->count()],
                ['label' => 'Disetujui', 'value' => (string) $allSubmissions->where('status', 'approved')->count()],
            ];

            $supervisorNames = [];
        } else {
            $stats = [];
            $supervisorNames = [];
        }

        return view('livewire.profile-page', compact('stats', 'availableLecturers', 'supervisorNames'));
    }
}
