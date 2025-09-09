<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <div class="flex">
        <aside class="w-64 min-h-screen bg-base-100 border-r border-base-300 p-4 space-y-2">
            <div class="text-sm font-semibold mb-2">Navigation</div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost justify-start">Dashboard</a>
            <a href="{{ route('admin.branches') }}" class="btn btn-ghost justify-start">Branches</a>
            <a href="{{ route('admin.positions') }}" class="btn btn-ghost justify-start">Positions</a>
            <a href="{{ route('admin.employees') }}" class="btn btn-ghost justify-start">Employees</a>
            <a href="{{ route('admin.requests') }}" class="btn btn-ghost justify-start">Requests</a>
            <a href="{{ route('admin.reports') }}" class="btn btn-ghost justify-start">Reports</a>
            <a href="{{ route('admin.calendar') }}" class="btn btn-ghost justify-start">Calendar</a>
            <a href="{{ route('admin.shift-hour') }}" class="btn btn-ghost justify-start">Shift Hour</a>
            <a href="{{ route('admin.shift-templates') }}" class="btn btn-ghost justify-start">Shift Templates</a>
            <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-base-300 mt-2">
                @csrf
                <button class="btn btn-ghost justify-start w-full">Logout</button>
            </form>
        </aside>
        <main class="flex-1 p-6 space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-base-100 border border-base-300 rounded p-4">
                    <div class="text-sm opacity-70">Total Employees</div>
                    <div class="text-2xl font-semibold">{{ $totalEmployees }}</div>
                </div>
                <div class="bg-base-100 border border-base-300 rounded p-4">
                    <div class="text-sm opacity-70">Clocked In Today</div>
                    <div class="text-2xl font-semibold">{{ $todayClockedIn }}</div>
                </div>
                <div class="bg-base-100 border border-base-300 rounded p-4">
                    <div class="text-sm opacity-70">Pending Requests</div>
                    <div class="text-2xl font-semibold">{{ $pendingRequests }}</div>
                </div>
            </div>

            <!-- All Employees Section -->
            <div class="bg-base-100 border border-base-300 rounded p-6">
                <h2 class="text-xl font-semibold mb-4">All Employees</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>NIP</th>
                                <th>Branch</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr>
                                <td class="font-medium">{{ $employee->name }}</td>
                                <td>{{ $employee->nip ?? '-' }}</td>
                                <td>{{ $employee->branch->name ?? '-' }}</td>
                                <td>{{ $employee->position->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $employee->employment_status === 'active' ? 'badge-success' : 'badge-error' }}">
                                        {{ ucfirst($employee->employment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Requests Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Absent Requests -->
                <div class="bg-base-100 border border-base-300 rounded p-6">
                    <h2 class="text-xl font-semibold mb-4">Pending Absent Requests</h2>
                    @if($pendingAbsentRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingAbsentRequests as $request)
                            <div class="border border-base-300 rounded p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium">{{ $request->employee->name }}</h3>
                                        <p class="text-sm opacity-70">{{ $request->employee->branch->name ?? '-' }} - {{ $request->employee->position->name ?? '-' }}</p>
                                    </div>
                                    <span class="badge badge-warning">Pending</span>
                                </div>
                                <div class="text-sm space-y-1">
                                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</p>
                                    <p><strong>Shift:</strong> {{ ucfirst($request->shift) }}</p>
                                    <p><strong>Reason:</strong> {{ $request->reason }}</p>
                                    <p><strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <form method="POST" action="{{ route('admin.requests.absent', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.requests.absent', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-error btn-sm">Reject</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-base-content/60 py-8">No pending absent requests</p>
                    @endif
                </div>

                <!-- Pending Shift Requests -->
                <div class="bg-base-100 border border-base-300 rounded p-6">
                    <h2 class="text-xl font-semibold mb-4">Pending Shift Requests</h2>
                    @if($pendingShiftRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingShiftRequests as $request)
                            <div class="border border-base-300 rounded p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium">{{ $request->employee->name }}</h3>
                                        <p class="text-sm opacity-70">{{ $request->employee->branch->name ?? '-' }} - {{ $request->employee->position->name ?? '-' }}</p>
                                    </div>
                                    <span class="badge badge-warning">Pending</span>
                                </div>
                                <div class="text-sm space-y-1">
                                    <p><strong>Actual Date:</strong> {{ \Carbon\Carbon::parse($request->actual_date)->format('M d, Y') }}</p>
                                    <p><strong>Request Date:</strong> {{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}</p>
                                    <p><strong>Shift:</strong> {{ ucfirst($request->shift) }}</p>
                                    <p><strong>Reason:</strong> {{ $request->reason }}</p>
                                    <p><strong>Requested:</strong> {{ $request->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <form method="POST" action="{{ route('admin.requests.shift', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.requests.shift', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-error btn-sm">Reject</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-base-content/60 py-8">No pending shift requests</p>
                    @endif
                </div>
            </div>

            <!-- Pending Schedule Change Requests -->
            <div class="bg-base-100 border border-base-300 rounded p-6">
                <h2 class="text-xl font-semibold mb-4">Pending Schedule Change Requests</h2>
                @if($pendingScheduleRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Branch</th>
                                    <th>Position</th>
                                    <th>Date</th>
                                    <th>Shift Hour</th>
                                    <th>Requested</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingScheduleRequests as $request)
                                <tr>
                                    <td class="font-medium">{{ $request->employee->name }}</td>
                                    <td>{{ $request->employee->branch->name ?? '-' }}</td>
                                    <td>{{ $request->employee->position->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</td>
                                    <td>{{ $request->shiftHour->name ?? '-' }}</td>
                                    <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('admin.requests.schedule', $request) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.requests.schedule', $request) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-error btn-sm">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-base-content/60 py-8">No pending schedule change requests</p>
                @endif
            </div>
        </main>
    </div>
</body>
</html>


