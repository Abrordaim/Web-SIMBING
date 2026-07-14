<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    protected $fillable = [
        'supervision_id',
        'title',
        'chapter',
        'type',
        'description',
        'file_path',
        'file_size',
        'status',
        'resolved',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function supervision(): BelongsTo
    {
        return $this->belongsTo(ThesisSupervision::class, 'supervision_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function decision(): HasOne
    {
        return $this->hasOne(SubmissionDecision::class);
    }
}
