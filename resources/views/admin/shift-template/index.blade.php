@extends('layouts.main')

@section('title', 'Shift Templates')
@section('page-title', 'Shift Templates')

@section('content')
    <!-- Search Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Shift Templates</h3>
        <form class="flex gap-4" method="get">
            <div class="flex-1">
                <input type="text" id="search" name="search" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Search shift templates...">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Filter</button>
        </form>
    </div>

    <!-- Create Shift Template Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Shift Template</h3>
        <form method="post" action="{{ route('admin.shift-templates.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                <select id="position_id" name="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                    <option value="">Select Position</option>
                    @foreach($positions as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="max_work_hour" class="block text-sm font-medium text-gray-700 mb-1">Max Working Hours *</label>
                <input type="text" id="max_work_hour" name="max_work_hour" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
            </div>
            <div>
                <label for="break_duration" class="block text-sm font-medium text-gray-700 mb-1">Break Duration *</label>
                <input type="text" id="break_duration" name="break_duration" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Create Template</button>
            </div>
        </form>
    </div>

    <!-- Shift Templates List Section -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Shift Templates List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Break Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shiftTemplates as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $t->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $t->position->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $t->max_work_hour }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $t->break_duration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="openEditModal({{ $t->id }}, '{{ $t->position_id }}', '{{ $t->max_work_hour }}', '{{ $t->break_duration }}')" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
                                    Edit
                                </button>
                                <form method="post" action="{{ route('admin.shift-templates.destroy', $t) }}" onsubmit="return confirm('Are you sure you want to delete this shift template?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-sm text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $shiftTemplates->links() }}
        </div>
    </div>

    <!-- Edit Shift Template Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeEditModal()"></div>
        <div class="relative mx-auto my-8 max-w-md bg-white rounded-lg shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Shift Template</h3>
            </div>
            <form id="editForm" method="post" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="edit_shift_template_id" name="shift_template_id">
                <div>
                    <label for="edit_position_id" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <select id="edit_position_id" name="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                        <option value="">Select Position</option>
                        @foreach($positions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_max_work_hour" class="block text-sm font-medium text-gray-700 mb-1">Max Working Hours</label>
                    <input type="text" id="edit_max_work_hour" name="max_work_hour" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                </div>
                <div>
                    <label for="edit_break_duration" class="block text-sm font-medium text-gray-700 mb-1">Break Duration</label>
                    <input type="text" id="edit_break_duration" name="break_duration" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, positionId, maxWorkHour, breakDuration) {
            document.getElementById('edit_shift_template_id').value = id;
            document.getElementById('edit_position_id').value = positionId;
            document.getElementById('edit_max_work_hour').value = maxWorkHour;
            document.getElementById('edit_break_duration').value = breakDuration;
            document.getElementById('editForm').action = `/admin/shift-templates/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
@endsection


