<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    /**
     * Hanya dosen pembimbing yang bisa confirm/reject meeting
     * dan hanya untuk jadwal mahasiswa binaannya.
     */
    public function confirm(User $user, Meeting $meeting): bool
    {
        if (!$user->isLecturer()) {
            return false;
        }

        return $user->lecturer?->supervisions()
            ->where('id', $meeting->supervision_id)
            ->exists() ?? false;
    }

    /**
     * Mahasiswa hanya bisa cancel meeting milik supervisinya sendiri.
     */
    public function cancel(User $user, Meeting $meeting): bool
    {
        if (!$user->isStudent()) {
            return false;
        }

        return $user->student?->supervisions()
            ->where('id', $meeting->supervision_id)
            ->exists() ?? false;
    }
}
