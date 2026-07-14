<?php

namespace App\Policies;

use App\Models\ThesisSupervision;
use App\Models\User;

class ThesisSupervisionPolicy
{
    /**
     * Dosen hanya bisa mengakses halaman detail mahasiswa binaannya.
     * Mahasiswa TIDAK boleh mengakses halaman ini sama sekali.
     */
    public function viewDetail(User $user, ThesisSupervision $supervision): bool
    {
        if (!$user->isLecturer()) {
            return false;
        }

        return $user->lecturer?->supervisions()
            ->where('id', $supervision->id)
            ->exists() ?? false;
    }
}
