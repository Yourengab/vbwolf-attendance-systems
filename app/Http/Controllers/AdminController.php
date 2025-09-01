<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\CompanyBranch;
use App\Models\Position;
use App\Models\RequestAbsent;
use App\Models\RequestShift;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        $totalEmployees = Employee::count();
        $todayClockedIn = Attendance::whereDate('date', $today)->whereNotNull('clock_in')->count();
        $pendingRequests = RequestAbsent::where('status', 'pending')->count() + RequestShift::where('status', 'pending')->count();
        return view('admin.dashboard', compact('totalEmployees','todayClockedIn','pendingRequests'));
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
            'name' => ['required','string','max:100'],
            'address' => ['nullable','string','max:255'],
        ]);
        CompanyBranch::create($data);
        return back()->with('success','Branch created.');
    }

    public function branchUpdate(CompanyBranch $branch, Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'address' => ['nullable','string','max:255'],
        ]);
        $branch->update($data);
        return back()->with('success','Branch updated.');
    }

    public function branchDestroy(CompanyBranch $branch)
    {
        $branch->delete();
        return back()->with('success','Branch deleted.');
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
            'name' => ['required','string','max:100'],
        ]);
        Position::create($data);
        return back()->with('success','Position created.');
    }

    public function positionUpdate(Position $position, Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
        ]);
        $position->update($data);
        return back()->with('success','Position updated.');
    }

    public function positionDestroy(Position $position)
    {
        $position->delete();
        return back()->with('success','Position deleted.');
    }

    public function employees(Request $request)
    {
        $q = Employee::with(['branch','position','user']);
        if ($request->filled('branch_id')) $q->where('branch_id', $request->get('branch_id'));
        if ($request->filled('position_id')) $q->where('position_id', $request->get('position_id'));
        if ($search = $request->get('search')) {
            $q->where(function($qq) use ($search){
                $qq->where('name','like',"%{$search}%")->orWhere('nip','like',"%{$search}%");
            });
        }
        $employees = $q->orderBy('name')->paginate(10)->withQueryString();
        $branches = CompanyBranch::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('admin.employees.index', compact('employees','branches','positions'));
    }

    public function employeeStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'nip' => ['nullable','string','max:50'],
            'branch_id' => ['required','exists:company_branches,id'],
            'position_id' => ['required','exists:positions,id'],
            'employment_status' => ['required','in:active,inactive'],
        ]);
        $user = \App\Models\User::firstOrCreate(
            ['email' => $request->input('email','employee'.uniqid().'@example.com')],
            ['password' => 'password','role' => 'employee','name' => $data['name']]
        );
        $data['user_id'] = $user->id;
        Employee::create($data);
        return back()->with('success','Employee created.');
    }

    public function employeeUpdate(Employee $employee, Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'nip' => ['nullable','string','max:50'],
            'branch_id' => ['required','exists:company_branches,id'],
            'position_id' => ['required','exists:positions,id'],
            'employment_status' => ['required','in:active,inactive'],
        ]);
        $employee->update($data);
        if ($employee->user) {
            $employee->user->update(['name' => $data['name']]);
        }
        return back()->with('success','Employee updated.');
    }

    public function employeeDestroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success','Employee deleted.');
    }

    public function requests(Request $request)
    {
        $shift = RequestShift::with('employee.user')->orderByDesc('id')->paginate(10, ['*'], 'shift_page')->withQueryString();
        $absent = RequestAbsent::with('employee.user')->orderByDesc('id')->paginate(10, ['*'], 'absent_page')->withQueryString();
        return view('admin.requests.index', compact('shift','absent'));
    }

    public function approveShift(RequestShift $requestShift, Request $request)
    {
        $action = $request->get('action');
        $requestShift->status = $action === 'approve' ? 'approved' : 'rejected';
        $requestShift->approved_by = Auth::id();
        $requestShift->approved_at = now();
        $requestShift->save();
        return back()->with('success','Shift request '.$requestShift->status.'.');
    }

    public function approveAbsent(RequestAbsent $requestAbsent, Request $request)
    {
        $action = $request->get('action');
        $requestAbsent->status = $action === 'approve' ? 'approved' : 'rejected';
        $requestAbsent->approved_by = Auth::id();
        $requestAbsent->approved_at = now();
        $requestAbsent->save();
        return back()->with('success','Day-off request '.$requestAbsent->status.'.');
    }

    public function deleteShift(RequestShift $requestShift)
    {
        $requestShift->delete();
        return back()->with('success','Shift request deleted.');
    }

    public function deleteAbsent(RequestAbsent $requestAbsent)
    {
        $requestAbsent->delete();
        return back()->with('success','Day-off request deleted.');
    }

    public function reports(Request $request)
    {
        $range = $request->get('range','daily');
        $start = Carbon::parse($request->get('start', now()->toDateString()));
        $end = Carbon::parse($request->get('end', now()->toDateString()));
        $q = Attendance::with('employee');
        if ($request->filled('employee_id')) $q->where('employee_id', $request->get('employee_id'));
        $q->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        $attendances = $q->orderBy('date','desc')->paginate(20)->withQueryString();
        $employees = Employee::orderBy('name')->get();
        return view('admin.reports.index', compact('attendances','employees','range','start','end'));
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
            ->whereBetween('date', [$start, $end]);
        
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
        ];
        
        return response()->json($stats);
    }
}


