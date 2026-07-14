<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecturer extends Model
{
    protected $fillable = [
        'user_id',
        'nidn',
        'department',
        'faculty',
        'specialization',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisions(): HasMany
    {
        return $this->hasMany(ThesisSupervision::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(SubmissionDecision::class);
    }
}
