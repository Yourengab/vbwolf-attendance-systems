@extends('layouts.main')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Total Employees</div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Clocked In Today</div>
            <div class="text-3xl font-bold text-gray-900">{{ $todayClockedIn }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Pending Requests</div>
            <div class="text-3xl font-bold text-gray-900">{{ $pendingRequests }}</div>
        </div>
    </div>

    <!-- All Employees Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">All Employees</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Name</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">NIP</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Branch</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Position</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $employee->name }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->nip ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->branch->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $employee->position->name ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->employment_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pending Absent Requests -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Pending Absent Requests</h2>
            @if($pendingAbsentRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingAbsentRequests as $request)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $request->employee->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $request->employee->branch->name ?? '-' }} - {{ $request->employee->position->name ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        </div>
                        <div class="text-sm space-y-2 text-gray-600">
                            <p><span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</p>
                            <p><span class="font-medium">Shift:</span> {{ ucfirst($request->shift) }}</p>
                            <p><span class="font-medium">Reason:</span> {{ $request->reason }}</p>
                            <p><span class="font-medium">Requested:</span> {{ $request->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <form method="POST" action="{{ route('admin.requests.absent', $request) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.requests.absent', $request) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No pending absent requests</p>
            @endif
        </div>

        <!-- Pending Shift Requests -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Pending Shift Requests</h2>
            @if($pendingShiftRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingShiftRequests as $request)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $request->employee->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $request->employee->branch->name ?? '-' }} - {{ $request->employee->position->name ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        </div>
                        <div class="text-sm space-y-2 text-gray-600">
                            <p><span class="font-medium">Actual Date:</span> {{ \Carbon\Carbon::parse($request->actual_date)->format('M d, Y') }}</p>
                            <p><span class="font-medium">Request Date:</span> {{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}</p>
                            <p><span class="font-medium">Shift:</span> {{ ucfirst($request->shift) }}</p>
                            <p><span class="font-medium">Reason:</span> {{ $request->reason }}</p>
                            <p><span class="font-medium">Requested:</span> {{ $request->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <form method="POST" action="{{ route('admin.requests.shift', $request) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.requests.shift', $request) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No pending shift requests</p>
            @endif
        </div>
    </div>

    <!-- Pending Schedule Change Requests -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Pending Schedule Change Requests</h2>
        @if($pendingScheduleRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Employee</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Branch</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Position</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Shift Hour</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Requested</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingScheduleRequests as $request)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $request->employee->name }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->employee->branch->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->employee->position->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->shiftHour->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $request->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex gap-3">
                                    <form method="POST" action="{{ route('admin.requests.schedule', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.requests.schedule', $request) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No pending schedule change requests</p>
        @endif
    </div>
@endsection
