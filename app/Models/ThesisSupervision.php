<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThesisSupervision extends Model
{
    protected $fillable = [
        'student_id',
        'lecturer_id',
        'title',
        'progress',
        'status',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'supervision_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'supervision_id');
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(TimelineEvent::class, 'supervision_id');
    }
}
