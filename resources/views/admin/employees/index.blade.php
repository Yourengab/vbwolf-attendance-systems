<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <div class="navbar bg-base-100 border-b border-base-300 px-4">
        <div class="flex-1">Employees</div>
        <div class="flex-none"><a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-ghost">Dashboard</a></div>
    </div>

    <main class="max-w-6xl mx-auto p-6 space-y-4">
        <form class="grid grid-cols-1 md:grid-cols-4 gap-2" method="get">
            <input type="text" name="search" value="{{ request('search') }}" class="input input-bordered" placeholder="Search name or NIP">
            <select name="branch_id" class="select select-bordered">
                <option value="">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
            <select name="position_id" class="select select-bordered">
                <option value="">All Positions</option>
                @foreach($positions as $p)
                    <option value="{{ $p->id }}" @selected(request('position_id')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-neutral">Filter</button>
        </form>

        <div class="bg-base-100 border border-base-300 rounded p-4">
            <form method="post" action="{{ route('admin.employees.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
                @csrf
                <input type="hidden" name="email" value=""> 
                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text">Name</span></label>
                    <input name="name" class="input input-bordered" required>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">NIP</span></label>
                    <input name="nip" class="input input-bordered">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Branch</span></label>
                    <select name="branch_id" class="select select-bordered" required>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Position</span></label>
                    <select name="position_id" class="select select-bordered" required>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Status</span></label>
                    <select name="employment_status" class="select select-bordered" required>
                        <option value="active">active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>

        <div class="bg-base-100 border border-base-300 rounded">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Email</th>
                            <th class="w-56"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $e)
                        <tr>
                            <td>{{ $e->nip }}</td>
                            <td>{{ $e->name }}</td>
                            <td>{{ $e->branch->name }}</td>
                            <td>{{ $e->position->name }}</td>
                            <td>{{ $e->employment_status }}</td>
                            <td>{{ $e->user?->email }}</td>
                            <td class="flex gap-2">
                                <form method="post" action="{{ route('admin.employees.update', $e) }}" class="grid grid-cols-3 gap-2">
                                    @csrf
                                    <input type="text" name="name" value="{{ $e->name }}" class="input input-bordered input-sm">
                                    <select name="employment_status" class="select select-bordered select-sm">
                                        <option value="active" @selected($e->employment_status==='active')>active</option>
                                        <option value="inactive" @selected($e->employment_status==='inactive')>inactive</option>
                                    </select>
                                    <button class="btn btn-sm">Save</button>
                                </form>
                                <form method="post" action="{{ route('admin.employees.destroy', $e) }}" onsubmit="return confirm('Delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-neutral">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $employees->links() }}</div>
        </div>
    </main>
</body>
</html>


