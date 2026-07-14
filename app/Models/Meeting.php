<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'supervision_id',
        'title',
        'date',
        'time_start',
        'location',
        'type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function supervision(): BelongsTo
    {
        return $this->belongsTo(ThesisSupervision::class, 'supervision_id');
    }
}
