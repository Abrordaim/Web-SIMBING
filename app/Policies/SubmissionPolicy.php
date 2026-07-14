<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Student bisa lihat submission milik supervisinya sendiri.
     * Lecturer bisa lihat submission mahasiswa binaannya.
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->isStudent()) {
            return $user->student?->supervisions()
                ->where('id', $submission->supervision_id)
                ->exists() ?? false;
        }

        if ($user->isLecturer()) {
            return $user->lecturer?->supervisions()
                ->where('id', $submission->supervision_id)
                ->exists() ?? false;
        }

        return false;
    }

    /**
     * Hanya mahasiswa yang boleh membuat submission baru.
     */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /**
     * Hanya dosen pembimbing yang bisa mengubah status submission
     * (approve/revision/reject), dan hanya untuk mahasiswa binaannya.
     */
    public function update(User $user, Submission $submission): bool
    {
        if (!$user->isLecturer()) {
            return false;
        }

        return $user->lecturer?->supervisions()
            ->where('id', $submission->supervision_id)
            ->exists() ?? false;
    }

    /**
     * Hanya dosen pembimbing yang bisa resolve/unresolve thread revisi
     * untuk mahasiswa binaannya.
     */
    public function resolve(User $user, Submission $submission): bool
    {
        if (!$user->isLecturer()) {
            return false;
        }

        return $user->lecturer?->supervisions()
            ->where('id', $submission->supervision_id)
            ->exists() ?? false;
    }
}
