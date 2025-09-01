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
            <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-base-300 mt-2">
                @csrf
                <button class="btn btn-ghost justify-start w-full">Logout</button>
            </form>
        </aside>
        <main class="flex-1 p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
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
        </main>
    </div>
</body>
</html>


