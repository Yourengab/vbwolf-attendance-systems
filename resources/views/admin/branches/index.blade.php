@extends('layouts.main')

@section('title', 'Branches')
@section('page-title', 'Branches')

@section('content')
    <!-- Search Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Branches</h3>
        <form class="flex gap-4" method="get">
            <div class="flex-1">
                <input type="text" id="search" name="search" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Search branches...">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Filter</button>
        </form>
    </div>

    <!-- Create Branch Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Branch</h3>
        <form method="post" action="{{ route('admin.branches.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Branch Name *</label>
                <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
            </div>
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Create Branch</button>
            </div>
        </form>
    </div>

    <!-- Branches List Section -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Branches List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($branches as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $b->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $b->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $b->address ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="openEditModal({{ $b->id }}, '{{ $b->name }}', '{{ $b->address }}')" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
                                    Edit
                                </button>
                                <form method="post" action="{{ route('admin.branches.destroy', $b) }}" onsubmit="return confirm('Are you sure you want to delete this branch?')" class="inline">
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
            {{ $branches->links() }}
        </div>
    </div>

    <!-- Edit Branch Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeEditModal()"></div>
        <div class="relative mx-auto my-8 max-w-md bg-white rounded-lg shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Branch</h3>
            </div>
            <form id="editForm" method="post" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="edit_branch_id" name="branch_id">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Branch Name</label>
                    <input type="text" id="edit_name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                </div>
                <div>
                    <label for="edit_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="edit_address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, address) {
            document.getElementById('edit_branch_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_address').value = address || '';
            document.getElementById('editForm').action = `/admin/branches/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
@endsection


