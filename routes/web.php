<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/employee', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::post('/employee/clock-in', [EmployeeController::class, 'clockIn'])->name('employee.clock_in');
    Route::post('/employee/clock-out', [EmployeeController::class, 'clockOut'])->name('employee.clock_out');
    Route::post('/employee/leave-start', [EmployeeController::class, 'leaveStart'])->name('employee.leave_start');
    Route::post('/employee/leave-end', [EmployeeController::class, 'leaveEnd'])->name('employee.leave_end');
    Route::post('/employee/overtime-start', [EmployeeController::class, 'overtimeStart'])->name('employee.overtime_start');
    Route::post('/employee/overtime-end', [EmployeeController::class, 'overtimeEnd'])->name('employee.overtime_end');
    Route::post('/employee/request-dayoff', [EmployeeController::class, 'requestDayOff'])->name('employee.request_dayoff');
    Route::post('/employee/request-shift', [EmployeeController::class, 'requestShiftChange'])->name('employee.request_shift');
    Route::post('/employee/shift-schedule', [EmployeeController::class, 'shiftSchedule'])->name('employee.shift-schedule');
    Route::post('/employee/profile', [EmployeeController::class, 'updateProfile'])->name('employee.update_profile');
    // Admin area
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit', [AdminController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
        Route::get('/branches', [AdminController::class, 'branches'])->name('branches');
        Route::post('/branches', [AdminController::class, 'branchStore'])->name('branches.store');
        Route::post('/branches/{branch}', [AdminController::class, 'branchUpdate'])->name('branches.update');
        Route::delete('/branches/{branch}', [AdminController::class, 'branchDestroy'])->name('branches.destroy');
        Route::get('/positions', [AdminController::class, 'positions'])->name('positions');
        Route::post('/positions', [AdminController::class, 'positionStore'])->name('positions.store');
        Route::post('/positions/{position}', [AdminController::class, 'positionUpdate'])->name('positions.update');
        Route::delete('/positions/{position}', [AdminController::class, 'positionDestroy'])->name('positions.destroy');
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
        Route::post('/employees', [AdminController::class, 'employeeStore'])->name('employees.store');
        Route::post('/employees/{employee}', [AdminController::class, 'employeeUpdate'])->name('employees.update');
        Route::delete('/employees/{employee}', [AdminController::class, 'employeeDestroy'])->name('employees.destroy');
        Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
        Route::post('/requests/shift/{requestShift}', [AdminController::class, 'approveShift'])->name('requests.shift');
        Route::post('/requests/schedule/{requestSchedule}', [AdminController::class, 'approveSchedule'])->name('requests.schedule');
        Route::post('/requests/absent/{requestAbsent}', [AdminController::class, 'approveAbsent'])->name('requests.absent');
        Route::delete('/requests/shift/{requestShift}', [AdminController::class, 'deleteShift'])->name('requests.shift.delete');
        Route::delete('/requests/absent/{requestAbsent}', [AdminController::class, 'deleteAbsent'])->name('requests.absent.delete');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/calendar', [AdminController::class, 'calendar'])->name('calendar');
        Route::get('/calendar/events', [AdminController::class, 'calendarEvents'])->name('calendar.events');
        Route::get('/calendar/stats', [AdminController::class, 'calendarStats'])->name('calendar.stats');
        Route::get('/shift-hours', [AdminController::class, 'shiftHours'])->name('shift-hour');
        Route::post('/shift-hours', [AdminController::class, 'shiftHourStore'])->name('shift-hour.store');
        Route::post('/shift-hours/{shiftHour}', [AdminController::class, 'shiftHourUpdate'])->name('shift-hour.update');
        Route::delete('/shift-hours/{shiftHour}', [AdminController::class, 'shiftHourDestroy'])->name('shift-hour.destroy');
        Route::get('/shift-templates', [AdminController::class, 'shiftTemplates'])->name('shift-templates');
        Route::post('/shift-templates', [AdminController::class, 'shiftTemplateStore'])->name('shift-templates.store');
        Route::post('/shift-templates/{shiftTemplate}', [AdminController::class, 'shiftTemplateUpdate'])->name('shift-templates.update');
        Route::delete('/shift-templates/{shiftTemplate}', [AdminController::class, 'shiftTemplateDestroy'])->name('shift-templates.destroy');
        

    });
});
