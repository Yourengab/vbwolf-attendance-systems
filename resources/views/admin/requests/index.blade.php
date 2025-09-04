<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-base-200 text-base-content">
    <div class="navbar bg-base-100 border-b border-base-300 px-4">
        <div class="flex-1">Requests</div>
        <div class="flex-none"><a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-ghost">Dashboard</a></div>
    </div>

    <main class="max-w-6xl mx-auto p-6 space-y-6">
        <section class="bg-base-100 border border-base-300 rounded p-4">
            <h2 class="font-semibold mb-3">Shift Change Requests</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Actual Date</th>
                            <th>Request Date</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shift as $r)
                        <tr>
                            <td>{{ $r->employee->name }} ({{ $r->employee->nip }})</td>
                            <td>{{ $r->actual_date }}</td>
                            <td>{{ $r->request_date }}</td>
                            <td>{{ $r->shift }}</td>
                            <td>{{ $r->status }}</td>
                            <td class="flex gap-2">
                                <form method="POST" action="{{ route('admin.requests.shift', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.shift', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-sm btn-neutral">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-2">{{ $shift->links() }}</div>
        </section>

        <section class="bg-base-100 border border-base-300 rounded p-4">
            <h2 class="font-semibold mb-3">Day-Off Requests</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absent as $r)
                        <tr>
                            <td>{{ $r->employee->name }} ({{ $r->employee->nip }})</td>
                            <td>{{ $r->date }}</td>
                            <td>{{ $r->shift }}</td>
                            <td>{{ $r->status }}</td>
                            <td class="flex gap-2">
                                <form method="POST" action="{{ route('admin.requests.absent', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.absent', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-sm btn-neutral">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-2">{{ $absent->links() }}</div>
        </section>

        <section>
            <h2 class="font-semibold mb-3">Shift Schedule Request</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Shift Hour</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $r)
                        <tr>
                            <td>{{ $r->employee->name }} ({{ $r->employee->nip }})</td>
                            <td>{{ $r->date }}</td>
                            <td>{{ $r->shiftHour->name }}</td>
                            <td>{{ $r->status }}</td>
                            <td class="flex gap-2">
                                <form method="POST" action="{{ route('admin.requests.schedule', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.requests.schedule', $r) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-sm btn-neutral">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        </section>
    </main>
</body>
</html>


