<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <div class="navbar bg-base-100 border-b border-base-300 px-4">
        <div class="flex-1">Reports</div>
        <div class="flex-none"><a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-ghost">Dashboard</a></div>
    </div>

    <main class="max-w-6xl mx-auto p-6 space-y-4">
        <form method="get" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text">Employee</span></label>
                <select name="employee_id" class="select select-bordered">
                    <option value="">All</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" @selected(request('employee_id')==$e->id)>{{ $e->name }} ({{ $e->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text">Start</span></label>
                <input type="date" name="start" value="{{ request('start', $start->toDateString()) }}" class="input input-bordered">
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text">End</span></label>
                <input type="date" name="end" value="{{ request('end', $end->toDateString()) }}" class="input input-bordered">
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text">Range</span></label>
                <select name="range" class="select select-bordered">
                    <option value="daily" @selected($range==='daily')>Daily</option>
                    <option value="weekly" @selected($range==='weekly')>Weekly</option>
                    <option value="monthly" @selected($range==='monthly')>Monthly</option>
                </select>
            </div>
            <div>
                <button class="btn btn-neutral w-full">Filter</button>
            </div>
        </form>

        <div class="bg-base-100 border border-base-300 rounded">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Total Work</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $a)
                        <tr>
                            <td>{{ $a->date }}</td>
                            <td>{{ $a->employee->name }} ({{ $a->employee->nip }})</td>
                            <td>{{ $a->clock_in }}</td>
                            <td>{{ $a->clock_out }}</td>
                            <td>{{ number_format($a->total_work_hours, 2) }} hours</td>
                            <td>
                                @if($a->photo)
                                    <a href="{{ Storage::url($a->photo) }}" class="link" target="_blank">View</a>
                                @else - @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-2">{{ $attendances->links() }}</div>
        </div>
    </main>
</body>
</html>


