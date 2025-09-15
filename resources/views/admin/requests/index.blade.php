@extends('layouts.main')

@section('title', 'Requests')
@section('page-title', 'Requests')

@section('content')
    <!-- Shift Change Requests Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Shift Change Requests</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shift as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $r->employee->name }} <span class="text-gray-500">({{ $r->employee->nip }})</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->actual_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->request_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->shift }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $r->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($r->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($r->status === 'pending')
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('admin.requests.shift', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.shift', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-gray-600 hover:bg-gray-700 rounded-md transition-colors">Reject</button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-500">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $shift->links() }}
        </div>
    </div>

    <!-- Day-Off Requests Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Day-Off Requests</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($absent as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $r->employee->name }} <span class="text-gray-500">({{ $r->employee->nip }})</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->shift }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $r->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($r->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($r->status === 'pending')
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('admin.requests.absent', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.absent', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-gray-600 hover:bg-gray-700 rounded-md transition-colors">Reject</button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-500">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $absent->links() }}
        </div>
    </div>

    <!-- Shift Schedule Requests Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Shift Schedule Requests</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift Hour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedule as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $r->employee->name }} <span class="text-gray-500">({{ $r->employee->nip }})</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r->shiftHour->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $r->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($r->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($r->status === 'pending')
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('admin.requests.schedule', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.schedule', $r) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-3 py-1 text-sm text-white bg-gray-600 hover:bg-gray-700 rounded-md transition-colors">Reject</button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-500">{{ ucfirst($r->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


