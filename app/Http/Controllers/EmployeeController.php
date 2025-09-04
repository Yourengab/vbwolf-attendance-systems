<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Overtime;
use App\Models\Position;
use App\Models\Attendance;
use App\Models\RequestShift;
use Illuminate\Http\Request;
use App\Models\RequestAbsent;
use App\Models\ShiftSchedule;
use App\Models\ShiftTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Permission as LeavePermission;
use App\Models\ShiftHour;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee()->with(['attendances' => function ($q) {
            $q->orderByDesc('date');
        }])->firstOrFail();

        $today = now('Asia/Makassar')->toDateString();
        $todayAttendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => $today,
        ]);

        $leaves = LeavePermission::where('attendance_id', $todayAttendance->id)->orderBy('start_time')->get();
        $overtimes = Overtime::where('attendance_id', $todayAttendance->id)->orderBy('start_time')->get();
        $shiftHours = ShiftHour::orderBy('id')->get();

        // Compute durations
        $clockInAt = $todayAttendance->clock_in ? Carbon::parse($todayAttendance->clock_in) : null;
        $clockOutAt = $todayAttendance->clock_out ? Carbon::parse($todayAttendance->clock_out) : null;

        $leaveMinutes = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_time);
            $end = $leave->end_time ? Carbon::parse($leave->end_time) : Carbon::now('Asia/Makassar');
            $leaveMinutes += max(0, $end->diffInMinutes($start));
        }

        $workMinutes = 0;
        if ($clockInAt) {
            $end = $clockOutAt ?: Carbon::now('Asia/Makassar');
            $workMinutes = max(0, $end->diffInMinutes($clockInAt) - $leaveMinutes);
        }

        $overtimeMinutes = 0;
        foreach ($overtimes as $ot) {
            $start = Carbon::parse($ot->start_time);
            $end = $ot->end_time ? Carbon::parse($ot->end_time) : Carbon::now('Asia/Makassar');
            $overtimeMinutes += max(0, $end->diffInMinutes($start));
        }

        $formatMinutes = function (int $minutes): string {
            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;
            if ($hours > 0 && $mins > 0) return $hours . 'h ' . $mins . 'm';
            if ($hours > 0) return $hours . 'h';
            return $mins . 'm';
        };

        $recap = [
            'work' => $formatMinutes($workMinutes),
            'leave' => $formatMinutes($leaveMinutes),
            'overtime' => $formatMinutes($overtimeMinutes),
        ];

        return view('employee.dashboard', compact('user', 'employee', 'todayAttendance', 'leaves', 'overtimes', 'recap', 'shiftHours'));
    }

    public function clockIn(Request $request)
    {
        $employee = Auth::user()->employee;
        $attendance = Attendance::firstOrCreate([
            'employee_id' => $employee->id,
            'date' => now('Asia/Makassar')->toDateString(),
        ]);
        if ($attendance->clock_in) {
            return back()->with('error', 'Already clocked in today.');
        }
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'photo_data' => ['nullable', 'string'],
        ]);

        if (!$request->hasFile('photo') && !$request->filled('photo_data')) {
            return back()->with('error', 'Photo is required to clock in.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        } elseif ($request->filled('photo_data')) {
            $data = $request->input('photo_data');
            if (preg_match('/^data:image\/(png|jpeg);base64,/', $data, $matches)) {
                $data = substr($data, strpos($data, ',') + 1);
                $data = base64_decode($data);
                $ext = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                $filename = 'attendance_photos/' . uniqid('att_') . '.' . $ext;
                Storage::disk('public')->put($filename, $data);
                $photoPath = $filename;
            }
        }

        if (!$photoPath) {
            return back()->with('error', 'Invalid photo data.');
        }

        // dd(now('Asia/Makassar'));
        $attendance->clock_in = now('Asia/Makassar');
        $attendance->photo = $photoPath;
        $attendance->save();
        return back()->with('success', 'Clocked in.');
    }

    public function clockOut(Request $request)
    {
        $employee = Auth::user()->employee;
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now('Asia/Makassar')->toDateString())
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'You must clock in first.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Already clocked out.');
        }

        $attendance->clock_out = now('Asia/Makassar');
        $attendance->save();

        $maxHour = ShiftTemplate::where('position_id', $employee->position_id)->first();

        // Hitung durasi kerja
        $clockIn  = Carbon::parse($attendance->clock_in, 'Asia/Makassar');
        $clockOut = Carbon::parse($attendance->clock_out . 'Asia/Makassar');


        $durationMinutes = $clockIn->diffInMinutes($clockOut);
        if ($durationMinutes > $maxHour->max_work_hour) {
            $threshold = 60;
            if ($durationMinutes > $threshold) {
                $overtimeStart = $clockIn->copy()->addMinutes($threshold);
                $overtimeEnd   = $clockOut;

                Overtime::create([
                    'attendance_id' => $attendance->id,
                    'start_time'    => $overtimeStart,
                    'end_time'      => $overtimeEnd,
                ]);
            }
        }




        return back()->with('success', 'Clocked out.');
    }


    public function leaveStart(Request $request)
    {
        $employee = Auth::user()->employee;
        $attendance = Attendance::where('employee_id', $employee->id)->where('date', now('Asia/Makassar')->toDateString())->first();
        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'Clock in first.');
        }
        // Only one leave session per day
        if (LeavePermission::where('attendance_id', $attendance->id)->exists()) {
            return back()->with('error', 'Only one leave session per day.');
        }
        LeavePermission::create([
            'attendance_id' => $attendance->id,
            'start_time' => now('Asia/Makassar'),
            'reason' => $request->reason,
        ]);
        return back()->with('success', 'Leave started.');
    }

    public function leaveEnd(Request $request)
    {
        $employee = Auth::user()->employee;
        $attendance = Attendance::where('employee_id', $employee->id)->where('date', now('Asia/Makassar')->toDateString())->first();
        if (!$attendance) {
            return back()->with('error', 'No attendance for today.');
        }
        $leave = LeavePermission::where('attendance_id', $attendance->id)->whereNull('end_time')->first();
        if (!$leave) {
            return back()->with('error', 'No active leave.');
        }
        $leave->end_time = now('Asia/Makassar');
        $leave->save();
        return back()->with('success', 'Leave ended.');
    }

    // public function overtimeStart(Request $request)
    // {
    //     $employee = Auth::user()->employee;
    //     $attendance = Attendance::where('employee_id', $employee->id)->where('date', now('Asia/Makassar')->toDateString())->first();
    //     if (!$attendance || !$attendance->clock_out) {
    //         return back()->with('error', 'Overtime only after clock-out.');
    //     }
    //     // Only one overtime session per day
    //     if (Overtime::where('attendance_id', $attendance->id)->exists()) {
    //         return back()->with('error', 'Only one overtime session per day.');
    //     }
    //     Overtime::create([
    //         'attendance_id' => $attendance->id,
    //         'start_time' => now('Asia/Makassar'),
    //         'reason' => $request->reason
    //     ]);
    //     return back()->with('success', 'Overtime started.');
    // }

    // public function overtimeEnd(Request $request)
    // {
    //     $employee = Auth::user()->employee;
    //     $attendance = Attendance::where('employee_id', $employee->id)->where('date', now('Asia/Makassar')->toDateString())->first();
    //     if (!$attendance) {
    //         return back()->with('error', 'No attendance for today.');
    //     }
    //     $ot = Overtime::where('attendance_id', $attendance->id)->whereNull('end_time')->first();
    //     if (!$ot) {
    //         return back()->with('error', 'No active overtime.');
    //     }
    //     $ot->end_time = now('Asia/Makassar');
    //     $ot->save();
    //     return back()->with('success', 'Overtime ended.');
    // }

    public function requestDayOff(Request $request)
    {
        $employee = Auth::user()->employee;
        $data = $request->validate([
            'date' => ['required', 'date'],
            'shift' => ['required', 'in:morning,evening,night'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);
        RequestAbsent::create([
            'employee_id' => $employee->id,
            'date' => $data['date'],
            'shift' => $data['shift'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Day-off request submitted.');
    }

    public function requestShiftChange(Request $request)
    {
        $employee = Auth::user()->employee;
        $data = $request->validate([
            'actual_date' => ['required', 'date'],
            'request_date' => ['required', 'date'],
            'shift' => ['required', 'in:morning,evening,night'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);
        RequestShift::create([
            'employee_id' => $employee->id,
            'actual_date' => $data['actual_date'],
            'request_date' => $data['request_date'],
            'shift' => $data['shift'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
        ]);
        return back()->with('success', 'Shift change request submitted.');
    }
    public function shiftSchedule(Request $request)
    {
        $employee = Auth::user()->employee;
        $data = $request->validate([
            'date' => ['required', 'date'],
            'shift_hour_id' => ['required', 'exists:shift_hours,id'],
            'status' => ['required', 'in:pending,accepted'],
        ]);
       ShiftSchedule::create([
            'employee_id' => $employee->id,
            'date' => $data['date'],
            'shift_hour_id' => $data['shift_hour_id'],
            'status' => $data['status'],
        ]);
        return back()->with('success', 'Shift schedule submitted.');
    }
}
