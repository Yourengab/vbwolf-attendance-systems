<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <div class="navbar bg-base-100 shadow">
        <div class="flex-1 px-2">Attendance System</div>
        <div class="flex-none">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-ghost">Logout</button>
            </form>
        </div>
    </div>
    <div class="container mx-auto p-4">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Welcome, {{ $user->email }}</h2>
                <p>Role: <span class="badge badge-outline">{{ $user->role }}</span></p>
                <p class="text-sm opacity-70">This is a placeholder dashboard. We'll route admins and employees to their dashboards later.</p>
            </div>
        </div>
    </div>
</body>
</html>


