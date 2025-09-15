<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftHour extends Model
{
    protected $fillable = [
        'name',
    ];

    function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
    public function requestShifts()
    {
        return $this->hasMany(RequestShift::class);
    }
    public function requestAbsents()
    {
        return $this->hasMany(RequestAbsent::class);
    }
}
