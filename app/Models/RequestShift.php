<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestShift extends Model
{
    protected $fillable = [
        'employee_id',
        'actual_date',
        'request_date',
        'shift',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
