<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VB Wolf Attendance System')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @yield('head')
</head>
<body class="h-full bg-gray-50 text-gray-900">
    @php
        $isAdmin = str_contains(request()->route()->getPrefix() ?? '', 'admin');
    @endphp

    @if($isAdmin)
        <!-- Admin Layout with Sidebar -->
        <div class="flex h-full">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex flex-col">
                <div class="p-6 border-b border-gray-200">
                    <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile.edit') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Edit Admin Profile
                    </a>
                    <a href="{{ route('admin.branches') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.branches*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="building-2" class="w-5 h-5 mr-3"></i>
                        Branches
                    </a>
                    <a href="{{ route('admin.positions') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.positions*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                        Positions
                    </a>
                    <a href="{{ route('admin.employees') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.employees*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                        Employees
                    </a>
                    <a href="{{ route('admin.requests') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.requests*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                        Requests
                    </a>
                    <a href="{{ route('admin.reports') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i>
                        Reports
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.calendar*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="calendar" class="w-5 h-5 mr-3"></i>
                        Calendar
                    </a>
                    <a href="{{ route('admin.shift-hour') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.shift-hour*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="clock" class="w-5 h-5 mr-3"></i>
                        Shift Hours
                    </a>
                    <a href="{{ route('admin.shift-templates') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.shift-templates*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                        Shift Templates
                    </a>
                </nav>
                <div class="p-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                            <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Mobile Sidebar Overlay -->
            <div id="mobile-sidebar" class="fixed inset-0 z-40 hidden md:hidden">
                <div class="absolute inset-0 bg-gray-600 opacity-75" onclick="toggleMobileSidebar()"></div>
                <aside class="relative w-64 bg-white flex flex-col">
                    <div class="p-6 border-b border-gray-200">
                        <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
                    </div>
                    <nav class="flex-1 p-4 space-y-1">
                        <!-- Same nav items as desktop -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.branches') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.branches*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="building-2" class="w-5 h-5 mr-3"></i>
                            Branches
                        </a>
                        <a href="{{ route('admin.positions') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.positions*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                            Positions
                        </a>
                        <a href="{{ route('admin.employees') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.employees*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                            Employees
                        </a>
                        <a href="{{ route('admin.requests') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.requests*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                            Requests
                        </a>
                        <a href="{{ route('admin.reports') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i>
                            Reports
                        </a>
                        <a href="{{ route('admin.calendar') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.calendar*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="calendar" class="w-5 h-5 mr-3"></i>
                            Calendar
                        </a>
                        <a href="{{ route('admin.shift-hour') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.shift-hour*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="clock" class="w-5 h-5 mr-3"></i>
                            Shift Hours
                        </a>
                        <a href="{{ route('admin.shift-templates') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.shift-templates*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                            Shift Templates
                        </a>
                    </nav>
                    <div class="p-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                                <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navbar -->
                <header class="bg-white border-b border-gray-200 px-4 py-3 md:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button onclick="toggleMobileSidebar()" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                            <h2 class="ml-2 md:ml-0 text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">Welcome, Admin</span>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 md:p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <!-- Employee Layout with Navbar -->
        <div class="min-h-screen flex flex-col">
            <!-- Top Navbar -->
            <header class="bg-white border-b border-gray-200 px-4 py-3 md:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-lg font-semibold text-gray-900">Employee Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, {{ auth()->user()->name ?? 'Employee' }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button class="px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    @endif

    <script>
        lucide.createIcons();

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
</body>
</html>