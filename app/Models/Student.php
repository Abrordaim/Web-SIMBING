<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'nim',
        'semester',
        'department',
        'faculty',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervision(): HasOne
    {
        return $this->hasOne(ThesisSupervision::class);
    }

    public function supervisions(): HasMany
    {
        return $this->hasMany(ThesisSupervision::class);
    }
}
