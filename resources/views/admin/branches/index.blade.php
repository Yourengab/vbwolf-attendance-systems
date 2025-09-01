<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <div class="navbar bg-base-100 border-b border-base-300 px-4">
        <div class="flex-1">Branches</div>
        <div class="flex-none"><a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-ghost">Dashboard</a></div>
    </div>

    <main class="max-w-5xl mx-auto p-6 space-y-4">
        <form class="flex gap-2" method="get">
            <input type="text" name="search" value="{{ request('search') }}" class="input input-bordered w-full" placeholder="Search branches...">
            <button class="btn btn-neutral">Filter</button>
        </form>

        <div class="bg-base-100 border border-base-300 rounded p-4">
            <form method="post" action="{{ route('admin.branches.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                @csrf
                <div class="form-control">
                    <label class="label"><span class="label-text">Name</span></label>
                    <input name="name" class="input input-bordered" required>
                </div>
                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text">Address</span></label>
                    <input name="address" class="input input-bordered">
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th class="w-40"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>{{ $b->name }}</td>
                            <td>{{ $b->address }}</td>
                            <td class="flex gap-2">
                                <form method="post" action="{{ route('admin.branches.update', $b) }}" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" value="{{ $b->name }}" class="input input-bordered input-sm w-32">
                                    <input type="text" name="address" value="{{ $b->address }}" class="input input-bordered input-sm w-32">
                                    <button class="btn btn-sm">Save</button>
                                </form>
                                <form method="post" action="{{ route('admin.branches.destroy', $b) }}" onsubmit="return confirm('Delete this branch?')">
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
            <div class="p-3">{{ $branches->links() }}</div>
        </div>
    </main>
</body>
</html>


