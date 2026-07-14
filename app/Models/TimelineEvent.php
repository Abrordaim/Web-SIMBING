<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineEvent extends Model
{
    protected $fillable = [
        'supervision_id',
        'event',
        'type',
        'event_date',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function supervision(): BelongsTo
    {
        return $this->belongsTo(ThesisSupervision::class, 'supervision_id');
    }
}
