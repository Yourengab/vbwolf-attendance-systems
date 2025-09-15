<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'photo',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

public function getTotalWorkHoursAttribute()
{
    if (!$this->clock_in || !$this->clock_out) {
        return "0 hours 0 minutes";
    }

    $clockIn  = Carbon::parse($this->clock_in);
    $clockOut = Carbon::parse($this->clock_out);

    // 1) Gross menit
    $grossMinutes = $clockIn->diffInMinutes($clockOut);

    // 2) Aturan shift
    $st = optional($this->employee->position->shiftTemplates->first());
    $breakMinutes = (int) ($st->break_duration ?? 0);
    $maxWorkMinutes = (int) ($st->max_work_hour ?? 0);

    // 3) Total izin
    $leaveMinutes = $this->permissions->sum(function ($p) {
        if (!$p->end_time) return 0;
        return Carbon::parse($p->start_time)->diffInMinutes(Carbon::parse($p->end_time));
    });

    // 4) Total lembur
    $overtimeMinutes = $this->overtimes->sum(function ($o) {
        if (!$o->end_time) return 0;
        return Carbon::parse($o->start_time)->diffInMinutes(Carbon::parse($o->end_time));
    });

    // 5) Hitung total menit
    $totalMinutes = $grossMinutes - $breakMinutes - $leaveMinutes;

    // 6) Tambahkan lembur hanya jika melewati max work + 1 jam
    if ($maxWorkMinutes > 0 && $grossMinutes > ($maxWorkMinutes + 60)) {
        $totalMinutes += $overtimeMinutes;
    }

    $totalMinutes = max($totalMinutes, 0);

    // 7) Konversi ke jam & menit
    $hours = intdiv($totalMinutes, 60);  // jam utuh
    $minutes = $totalMinutes % 60;       // sisa menit

    return "{$hours} hours {$minutes} minutes";
}


}
