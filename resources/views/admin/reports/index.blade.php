@extends('layouts.main')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    <!-- Filter Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Reports</h3>
        <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select id="employee_id" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                    <option value="">All Employees</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" @selected(request('employee_id')==$e->id)>{{ $e->name }} ({{ $e->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start" name="start" value="{{ request('start', $start->toDateString()) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
            </div>
            <div>
                <label for="end" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end" name="end" value="{{ request('end', $end->toDateString()) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
            </div>
            <div>
                <label for="range" class="block text-sm font-medium text-gray-700 mb-1">Range</label>
                <select id="range" name="range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                    <option value="daily" @selected($range==='daily')>Daily</option>
                    <option value="weekly" @selected($range==='weekly')>Weekly</option>
                    <option value="monthly" @selected($range==='monthly')>Monthly</option>
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-1">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Filter</button>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Reports</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Work</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attendances as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $a->date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $a->employee->name }} <span class="text-gray-500">({{ $a->employee->nip }})</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $a->clock_in ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $a->clock_out ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $a->total_work_hours ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($a->photo)
                                <a href="{{ Storage::url($a->photo) }}" class="text-blue-600 hover:text-blue-800 hover:underline" target="_blank">View</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $attendances->links() }}
        </div>
    </div>
@endsection


