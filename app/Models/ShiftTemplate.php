<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftTemplate extends Model
{
    protected $fillable = [
        'position_id',
        'max_work_hour',
        'break_duration',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
