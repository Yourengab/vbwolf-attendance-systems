<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'shift_hour_id',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function shiftHour()
    {
        return $this->belongsTo(ShiftHour::class);
    }
}
