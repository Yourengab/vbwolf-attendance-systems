<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\ShiftHour;
use App\Models\Attendance;
use App\Models\RequestShift;
use Illuminate\Http\Request;
use App\Models\CompanyBranch;
use App\Models\RequestAbsent;
use App\Models\ShiftSchedule;
use App\Models\ShiftTemplate;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function editProfile()
    {
        $admin = Auth::user();
        return view('admin.profile.edit', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = User::find(Auth::id());
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($data['password'])) {
            $admin->password = bcrypt($data['password']);
        }
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->save();
        return redirect()->route('admin.profile.edit')->with('success', 'Informasi admin berhasil diupdate.');
    }
    public function dashboard()
    {
        $today = now()->toDateString();
        $totalEmployees = Employee::count();
        $todayClockedIn = Attendance::whereDate('date', $today)->whereNotNull('clock_in')->count();
        $pendingRequests = RequestAbsent::where('status', 'pending')->count() + RequestShift::where('status', 'pending')->count();
        
        // Get all employee data with relationships
        $employees = Employee::with(['branch', 'position', 'user'])->orderBy('name')->get();
        
        // Get pending absent requests
        $pendingAbsentRequests = RequestAbsent::with(['employee.branch', 'employee.position'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get pending shift requests
        $pendingShiftRequests = RequestShift::with(['employee.branch', 'employee.position'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get pending shift schedule requests
        $pendingScheduleRequests = ShiftSchedule::with(['employee.branch', 'employee.position', 'shiftHour'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalEmployees', 
            'todayClockedIn', 
            'pendingRequests',
            'employees',
            'pendingAbsentRequests',
            'pendingShiftRequests',
            'pendingScheduleRequests'
        ));
    }

    public function branches(Request $request)
    {
        $q = CompanyBranch::query();
        if ($search = $request->get('search')) {
            $q->where('name', 'like', "%{$search}%");
        }
        $branches = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.branches.index', compact('branches'));
    }

    public function branchStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
        CompanyBranch::create($data);
        return back()->with('success', 'Branch created.');
    }

    public function branchUpdate(CompanyBranch $branch, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
        $branch->update($data);
        return back()->with('success', 'Branch updated.');
    }

    public function branchDestroy(CompanyBranch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Branch deleted.');
    }

    public function positions(Request $request)
    {
        $q = Position::query();
        if ($search = $request->get('search')) {
            $q->where('name', 'like', "%{$search}%");
        }
        $positions = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.positions.index', compact('positions'));
    }

    public function positionStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);
        Position::create($data);
        return back()->with('success', 'Position created.');
    }

    public function positionUpdate(Position $position, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);
        $position->update($data);
        return back()->with('success', 'Position updated.');
    }

    public function positionDestroy(Position $position)
    {
        $position->delete();
        return back()->with('success', 'Position deleted.');
    }

    public function employees(Request $request)
    {
        $q = Employee::with(['branch', 'position', 'user']);
        if ($request->filled('branch_id')) $q->where('branch_id', $request->get('branch_id'));
        if ($request->filled('position_id')) $q->where('position_id', $request->get('position_id'));
        if ($search = $request->get('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%");
            });
        }
        $employees = $q->orderBy('name')->paginate(10)->withQueryString();
        $branches = CompanyBranch::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('admin.employees.index', compact('employees', 'branches', 'positions'));
    }

    public function employeeStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nip' => ['nullable', 'string', 'max:50'],
            'branch_id' => ['required', 'exists:company_branches,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'employment_status' => ['required', 'in:active,inactive'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);
        $data['user_id'] = $user->id;
        Employee::create($data);
        return back()->with('success', 'Employee created.');
    }

    public function employeeUpdate(Employee $employee, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:50'],
            'branch_id' => ['required', 'exists:company_branches,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'employment_status' => ['required', 'in:active,inactive'],
        ]);
        $employee->update($data);
        if ($employee->user) {
            $employee->user->update(['name' => $data['name']]);
        }
        return back()->with('success', 'Employee updated.');
    }

    public function employeeDestroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Employee deleted.');
    }

    public function requests(Request $request)
    {
        $shift = RequestShift::with('employee.user')->orderByDesc('id')->paginate(10, ['*'], 'shift_page')->withQueryString();
        $absent = RequestAbsent::with('employee.user')->orderByDesc('id')->paginate(10, ['*'], 'absent_page')->withQueryString();
        $schedule = ShiftSchedule::with('employee.user')->orderByDesc('id')->paginate(10, ['*'], 'schedule_page')->withQueryString();
        return view('admin.requests.index', compact('shift', 'absent', 'schedule'));
    }

    public function approveShift(RequestShift $requestShift, Request $request)
    {
        $action = $request->get('action');
        $requestShift->status = $action === 'approve' ? 'approved' : 'rejected';
        $requestShift->approved_by = Auth::id();
        $requestShift->approved_at = now();
        $requestShift->save();
        return back()->with('success', 'Shift request ' . $requestShift->status . '.');
    }

    public function approveSchedule(ShiftSchedule $requestSchedule, Request $request)
    {
        $action = $request->get('action');
        $requestSchedule->status = $action === 'approve' ? 'approved' : 'rejected';
        if ($action === 'approve') {
            // Apply the schedule change
            $shiftHour = ShiftHour::find($requestSchedule->shift_hour_id);
            if ($shiftHour) {
                $existingSchedule = ShiftSchedule::where('employee_id', $requestSchedule->employee_id)
                    ->where('date', $requestSchedule->date)
                    ->first();
                if ($existingSchedule) {
                    $existingSchedule->shift_hour_id = $shiftHour->id;
                    $existingSchedule->save();
                } else {
                    ShiftSchedule::create([
                        'employee_id' => $requestSchedule->employee_id,
                        'date' => $requestSchedule->date,
                        'shift_hour_id' => $shiftHour->id,
                        'status' => 'approved',
                    ]);
                }
            }
        }
        $requestSchedule->save();
        return back()->with('success', 'Shift schedule request ' . $requestSchedule->status . '.');
    }

    public function approveAbsent(RequestAbsent $requestAbsent, Request $request)
    {
        $action = $request->get('action');
        $requestAbsent->status = $action === 'approve' ? 'approved' : 'rejected';
        $requestAbsent->approved_by = Auth::id();
        $requestAbsent->approved_at = now();
        $requestAbsent->save();
        return back()->with('success', 'Day-off request ' . $requestAbsent->status . '.');
    }

    public function deleteShift(RequestShift $requestShift)
    {
        $requestShift->delete();
        return back()->with('success', 'Shift request deleted.');
    }

    public function deleteAbsent(RequestAbsent $requestAbsent)
    {
        $requestAbsent->delete();
        return back()->with('success', 'Day-off request deleted.');
    }

    public function reports(Request $request) 
{
    $range = $request->get('range', 'daily');
    $start = Carbon::parse($request->get('start', now()->toDateString()));
    $end = Carbon::parse($request->get('end', now()->toDateString()));

    $q = Attendance::with([
        'employee.position.shiftTemplates', 
        'permissions',
        'overtimes'
    ]);

    if ($request->filled('employee_id')) {
        $q->where('employee_id', $request->get('employee_id'));
    }

    $q->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
    $attendances = $q->orderBy('date', 'desc')->paginate(20)->withQueryString();

    $employees = Employee::orderBy('name')->get();

    return view('admin.reports.index', compact('attendances', 'employees', 'range', 'start', 'end'));
}


    public function calendar(Request $request)
    {
        $employees = Employee::with('branch', 'position')->orderBy('name')->get();
        $branches = CompanyBranch::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        return view('admin.calendar.index', compact('employees', 'branches', 'positions'));
    }

    public function calendarEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $employeeId = $request->get('employee_id');
        $branchId = $request->get('branch_id');
        $positionId = $request->get('position_id');

        $events = [];

        // Build base query for filtering employees
        $employeeQuery = Employee::query();
        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }
        if ($positionId) {
            $employeeQuery->where('position_id', $positionId);
        }

        $filteredEmployeeIds = $employeeQuery->pluck('id')->toArray();

        // Get attendances
        $attendanceQuery = Attendance::with('employee.user')
            ->whereBetween('date', [$start, $end])->where('clock_in', '!=', null);

        if ($employeeId) {
            $attendanceQuery->where('employee_id', $employeeId);
        } elseif (!empty($filteredEmployeeIds)) {
            $attendanceQuery->whereIn('employee_id', $filteredEmployeeIds);
        }

        $attendances = $attendanceQuery->get();

        foreach ($attendances as $attendance) {
            $events[] = [
                'id' => 'attendance_' . $attendance->id,
                'title' => $attendance->employee->name . ' - Present',
                'start' => $attendance->date,
                'end' => $attendance->date,
                'backgroundColor' => '#10b981',
                'borderColor' => '#10b981',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'attendance',
                    'employee' => $attendance->employee->name,
                    'clock_in' => $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : null,
                    'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : null,
                ]
            ];
        }

        // Get shift change requests
        $shiftQuery = RequestShift::with('employee.user')
            ->whereBetween('actual_date', [$start, $end])
            ->where('status', 'approved');

        if ($employeeId) {
            $shiftQuery->where('employee_id', $employeeId);
        } elseif (!empty($filteredEmployeeIds)) {
            $shiftQuery->whereIn('employee_id', $filteredEmployeeIds);
        }

        $shifts = $shiftQuery->get();

        foreach ($shifts as $shift) {
            $events[] = [
                'id' => 'shift_' . $shift->id,
                'title' => $shift->employee->name . ' - ' . ucfirst($shift->shift) . ' Shift',
                'start' => $shift->actual_date,
                'end' => $shift->actual_date,
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#3b82f6',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'shift',
                    'employee' => $shift->employee->name,
                    'shift' => $shift->shift,
                    'reason' => $shift->reason,
                ]
            ];
        }

        // Get absent requests
        $absentQuery = RequestAbsent::with('employee.user')
            ->whereBetween('date', [$start, $end])
            ->where('status', 'approved');

        if ($employeeId) {
            $absentQuery->where('employee_id', $employeeId);
        } elseif (!empty($filteredEmployeeIds)) {
            $absentQuery->whereIn('employee_id', $filteredEmployeeIds);
        }

        $absents = $absentQuery->get();

        foreach ($absents as $absent) {
            $events[] = [
                'id' => 'absent_' . $absent->id,
                'title' => $absent->employee->name . ' - Absent',
                'start' => $absent->date,
                'end' => $absent->date,
                'backgroundColor' => '#ef4444',
                'borderColor' => '#ef4444',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'absent',
                    'employee' => $absent->employee->name,
                    'shift' => $absent->shift,
                    'reason' => $absent->reason,
                ]
            ];
        }

        // Get pending requests
        $pendingShifts = RequestShift::with('employee.user')
            ->whereBetween('actual_date', [$start, $end])
            ->where('status', 'pending');

        $pendingAbsents = RequestAbsent::with('employee.user')
            ->whereBetween('date', [$start, $end])
            ->where('status', 'pending');

        if ($employeeId) {
            $pendingShifts->where('employee_id', $employeeId);
            $pendingAbsents->where('employee_id', $employeeId);
        } elseif (!empty($filteredEmployeeIds)) {
            $pendingShifts->whereIn('employee_id', $filteredEmployeeIds);
            $pendingAbsents->whereIn('employee_id', $filteredEmployeeIds);
        }

        $pendingShifts = $pendingShifts->get();
        $pendingAbsents = $pendingAbsents->get();

        foreach ($pendingShifts as $shift) {
            $events[] = [
                'id' => 'pending_shift_' . $shift->id,
                'title' => $shift->employee->name . ' - Pending Shift Change',
                'start' => $shift->actual_date,
                'end' => $shift->actual_date,
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'pending_shift',
                    'employee' => $shift->employee->name,
                    'shift' => $shift->shift,
                    'reason' => $shift->reason,
                ]
            ];
        }

        foreach ($pendingAbsents as $absent) {
            $events[] = [
                'id' => 'pending_absent_' . $absent->id,
                'title' => $absent->employee->name . ' - Pending Absent Request',
                'start' => $absent->date,
                'end' => $absent->date,
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'pending_absent',
                    'employee' => $absent->employee->name,
                    'shift' => $absent->shift,
                    'reason' => $absent->reason,
                ]
            ];
        }

        // Get pending requests
        $shiftSchedule = ShiftSchedule::with('employee.user')
            ->whereBetween('date', [$start, $end])
            ->where('status', 'approved');

        if ($employeeId) {
            $shiftSchedule->where('employee_id', $employeeId);
        } elseif (!empty($filteredEmployeeIds)) {
            $shiftSchedule->whereIn('employee_id', $filteredEmployeeIds);
        }

        $shiftSchedule = $shiftSchedule->get();


        foreach ($shiftSchedule as $schedule) {
            $events[] = [
                'id' => 'shift_schedule' . $schedule->id,
                'title' => $schedule->employee->name . ' - Shift Schedule',
                'start' => $schedule->date,
                'end' => $schedule->date,
                'backgroundColor' => '#2332db',
                'borderColor' => '#2332db',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'shift_schedule',
                    'employee' => $schedule->employee->name,
                    'shift_hour' => $schedule->shiftHour ? $schedule->shiftHour->name : null,
                ]
            ];
        }

        return response()->json($events);
    }

    public function calendarStats(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $stats = [
            'total_days' => $end->diffInDays($start) + 1,
            'total_employees' => Employee::count(),
            'present_count' => Attendance::whereBetween('date', [$start->toDateString(), $end->toDateString()])->count(),
            'absent_count' => RequestAbsent::whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'approved')->count(),
            'shift_changes' => RequestShift::whereBetween('actual_date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'approved')->count(),
            'pending_requests' => RequestShift::whereBetween('actual_date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'pending')->count() +
                RequestAbsent::whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'pending')->count(),
            'shift_schedule' => ShiftSchedule::whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where('status', 'approved')->count(), 
        ];

        return response()->json($stats);
    }

    public function shiftHours(Request $request)
    {
        $q = ShiftHour::query();
        if ($search = $request->get('search')) {
            $q->where('name', 'like', "%{$search}%");
        }
        $shiftHours = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.shift-hour.index', compact('shiftHours'));
    }
    public function shiftHourStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);
        ShiftHour::create($data);
        return back()->with('success', 'Shift Hour created.');
    }
    public function shiftHourUpdate(ShiftHour $shiftHour, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);
        $shiftHour->update($data);
        return back()->with('success', 'Shift Hour updated.');
    }
    public function shiftHourDestroy(ShiftHour $shiftHour)
    {
        $shiftHour->delete();
        return back()->with('success', 'Shift Hour deleted.');
    }
    public function shiftTemplates(Request $request)
    {
        $q = ShiftTemplate::with('position');
        if ($search = $request->get('search')) {
            $q->where('name', 'like', "%{$search}%");
        }
        $shiftTemplates = $q->orderBy('id')->paginate(10)->withQueryString();
        $positions = Position::orderBy('name')->get();
        return view('admin.shift-template.index', compact('shiftTemplates', 'positions'));
    }
    public function shiftTemplateStore(Request $request)
    {
        $data = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'max_work_hour' => ['required', 'max:50'],
            'break_duration' => ['required', 'max:50'],
        ]);
        // dd($data);
        ShiftTemplate::create($data);
        return back()->with('success', 'Shift Template created.');
    }
    public function shiftTemplateUpdate(ShiftTemplate $shiftTemplate, Request $request)
    {
        $data = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'max_work_hour' => ['required', 'string', 'max:50'],
            'break_duration' => ['required', 'string', 'max:50'],
        ]);
        $shiftTemplate->update($data);
        return back()->with('success', 'Shift Template updated.');
    }
    public function shiftTemplateDestroy(ShiftTemplate $shiftTemplate)
    {
        $shiftTemplate->delete();
        return back()->with('success', 'Shift Template deleted.');
    }
}
