<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.10/index.global.min.js"></script>
    <style>
        .fc-event {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .fc-daygrid-event {
            margin: 1px 0;
        }
        .fc-event-title {
            font-weight: 500;
        }
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 600;
        }
        .fc-button {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }
        .fc-button:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        .fc-button-active {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
        }
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 0.5rem;
            }
            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }
        }
    </style>
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
        <main class="flex-1 p-6 space-y-4">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="stat bg-base-100 rounded-lg shadow">
                    <div class="stat-title">Total Employees</div>
                    <div class="stat-value text-primary" id="total-employees">-</div>
                </div>
                <div class="stat bg-base-100 rounded-lg shadow">
                    <div class="stat-title">Present Today</div>
                    <div class="stat-value text-success" id="present-count">-</div>
                </div>
                <div class="stat bg-base-100 rounded-lg shadow">
                    <div class="stat-title">Absent Today</div>
                    <div class="stat-value text-error" id="absent-count">-</div>
                </div>
                <div class="stat bg-base-100 rounded-lg shadow">
                    <div class="stat-title">Shift Changes</div>
                    <div class="stat-value text-info" id="shift-changes">-</div>
                </div>
                <div class="stat bg-base-100 rounded-lg shadow">
                    <div class="stat-title">Pending Requests</div>
                    <div class="stat-value text-warning" id="pending-requests">-</div>
                </div>
            </div>

            <div class="bg-base-100 border border-base-300 rounded p-4">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <select id="employee-filter" class="select select-bordered select-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <select id="branch-filter" class="select select-bordered select-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <select id="position-filter" class="select select-bordered select-sm">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                    <button id="filter-btn" class="btn btn-neutral btn-sm">Filter</button>
                    <button id="clear-filter-btn" class="btn btn-outline btn-sm">Clear</button>
                    <button id="refresh-btn" class="btn btn-ghost btn-sm">🔄</button>
                </div>
                <div class="border border-base-300 rounded h-[70vh] overflow-hidden relative">
                    <div id="calendar" class="h-full"></div>
                    <div id="calendar-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden">
                        <div class="loading loading-spinner loading-lg text-primary"></div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-4 flex flex-wrap items-center justify-between">
                    <div class="text-sm">
                        <div class="flex flex-wrap gap-4">
                            <span><span class="inline-block w-3 h-3 rounded bg-green-400 mr-1"></span>Present</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-blue-400 mr-1"></span>Shift Change</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-red-400 mr-1"></span>Absent</span>
                            <span><span class="inline-block w-3 h-3 rounded bg-orange-400 mr-1"></span>Pending Requests</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button id="help-btn" class="btn btn-ghost btn-sm">?</button>
                        <button id="export-btn" class="btn btn-outline btn-sm">Export Calendar</button>
                        <button id="print-btn" class="btn btn-outline btn-sm">Print View</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Event Details Modal -->
    <div id="event-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg" id="modal-title"></h3>
            <div id="modal-content" class="py-4"></div>
            <div class="modal-action">
                <button class="btn" onclick="closeEventModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="help-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Calendar Help & Keyboard Shortcuts</h3>
            <div class="py-4 space-y-4">
                <div>
                    <h4 class="font-semibold mb-2">Navigation</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><kbd class="kbd kbd-sm">Ctrl + ←</kbd> Previous period</div>
                        <div><kbd class="kbd kbd-sm">Ctrl + →</kbd> Next period</div>
                        <div><kbd class="kbd kbd-sm">Ctrl + Home</kbd> Go to today</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">View Switching</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div><kbd class="kbd kbd-sm">Ctrl + 1</kbd> Month view</div>
                        <div><kbd class="kbd kbd-sm">Ctrl + 2</kbd> Week view</div>
                        <div><kbd class="kbd kbd-sm">Ctrl + 3</kbd> List view</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Legend</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="inline-block w-3 h-3 rounded bg-green-400 mr-2"></span>Present - Employee attended work</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-blue-400 mr-2"></span>Shift Change - Approved shift change request</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-red-400 mr-2"></span>Absent - Approved absence request</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-orange-400 mr-2"></span>Pending - Requests awaiting approval</div>
                    </div>
                </div>
            </div>
            <div class="modal-action">
                <button class="btn" onclick="closeHelpModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let calendar;
            let currentFilters = {
                employee_id: '',
                branch_id: '',
                position_id: ''
            };

            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            
            if (!calendarEl) {
                console.error('Calendar element not found');
                return;
            }
            
            try {
                calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listWeek'
                },
                height: '100%',
                events: function(info, successCallback, failureCallback) {
                    // Fetch events from the server
                    fetchCalendarEvents(info.startStr, info.endStr, successCallback, failureCallback);
                },
                loading: function(isLoading) {
                    if (isLoading) {
                        document.getElementById('calendar-loading').classList.remove('hidden');
                    } else {
                        document.getElementById('calendar-loading').classList.add('hidden');
                    }
                },
                eventClick: function(info) {
                    showEventDetails(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltips or additional styling if needed
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    // Add custom tooltip
                    if (props.type === 'pending_shift' || props.type === 'pending_absent') {
                        info.el.style.border = '2px dashed #f59e0b';
                    }
                    
                    // Add custom styling based on event type
                    switch(props.type) {
                        case 'attendance':
                            info.el.style.borderLeft = '4px solid #10b981';
                            break;
                        case 'shift':
                            info.el.style.borderLeft = '4px solid #3b82f6';
                            break;
                        case 'absent':
                            info.el.style.borderLeft = '4px solid #ef4444';
                            break;
                        case 'pending_shift':
                        case 'pending_absent':
                            info.el.style.borderLeft = '4px solid #f59e0b';
                            break;
                    }
                    
                    // Add custom tooltip content
                    const tooltipContent = `
                        <div class="p-2 text-sm">
                            <div class="font-semibold">${event.title}</div>
                            <div class="text-gray-600">${props.type}</div>
                            ${props.employee ? `<div class="text-gray-600">Employee: ${props.employee}</div>` : ''}
                            ${props.shift ? `<div class="text-gray-600">Shift: ${props.shift}</div>` : ''}
                        </div>
                    `;
                    
                    // Create tooltip element
                    const tooltip = document.createElement('div');
                    tooltip.className = 'event-tooltip bg-white border border-gray-300 rounded shadow-lg opacity-0 pointer-events-none absolute z-50';
                    tooltip.innerHTML = tooltipContent;
                    tooltip.style.maxWidth = '200px';
                    
                    // Add tooltip to body
                    document.body.appendChild(tooltip);
                    
                    // Show tooltip on hover
                    info.el.addEventListener('mouseenter', function(e) {
                        tooltip.style.opacity = '1';
                        tooltip.style.left = e.pageX + 10 + 'px';
                        tooltip.style.top = e.pageY - 10 + 'px';
                    });
                    
                    // Hide tooltip on mouse leave
                    info.el.addEventListener('mouseleave', function() {
                        tooltip.style.opacity = '0';
                    });
                    
                    // Store tooltip reference for cleanup
                    info.el._tooltip = tooltip;
                },
                eventUnmount: function(info) {
                    // Clean up tooltips when events are removed
                    if (info.el._tooltip) {
                        info.el._tooltip.remove();
                        delete info.el._tooltip;
                    }
                },
                dayMaxEvents: true,
                moreLinkClick: 'popover',
                selectable: true,
                select: function(info) {
                    // Handle date selection if needed
                    console.log('Selected date:', info.startStr);
                },
                datesSet: function(info) {
                    // Update statistics when calendar view changes
                    loadCalendarStats();
                },
                height: 'auto',
                aspectRatio: 1.35,
                expandRows: true,
                nowIndicator: true,
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                    startTime: '08:00',
                    endTime: '17:00',
                },
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                slotDuration: '01:00:00',
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                dayHeaderFormat: {
                    weekday: 'short',
                    day: 'numeric'
                },
                firstDay: 1, // Monday
                weekNumbers: true,
                weekNumberCalculation: 'ISO'
            });

                calendar.render();
            } catch (error) {
                console.error('Error initializing calendar:', error);
                showErrorMessage('Failed to initialize calendar. Please refresh the page.');
                return;
            }

            // Load initial statistics
            loadCalendarStats();

            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'ArrowLeft':
                            e.preventDefault();
                            calendar.prev();
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            calendar.next();
                            break;
                        case 'Home':
                            e.preventDefault();
                            calendar.today();
                            break;
                        case '1':
                            e.preventDefault();
                            calendar.changeView('dayGridMonth');
                            break;
                        case '2':
                            e.preventDefault();
                            calendar.changeView('dayGridWeek');
                            break;
                        case '3':
                            e.preventDefault();
                            calendar.changeView('listWeek');
                            break;
                    }
                }
            });

            // Filter functionality
            document.getElementById('filter-btn').addEventListener('click', function() {
                currentFilters.employee_id = document.getElementById('employee-filter').value;
                currentFilters.branch_id = document.getElementById('branch-filter').value;
                currentFilters.position_id = document.getElementById('position-filter').value;
                
                // Refresh calendar with new filters
                calendar.refetchEvents();
            });

            document.getElementById('clear-filter-btn').addEventListener('click', function() {
                document.getElementById('employee-filter').value = '';
                document.getElementById('branch-filter').value = '';
                document.getElementById('position-filter').value = '';
                
                currentFilters = {
                    employee_id: '',
                    branch_id: '',
                    position_id: ''
                };
                
                calendar.refetchEvents();
            });

            document.getElementById('refresh-btn').addEventListener('click', function() {
                calendar.refetchEvents();
                loadCalendarStats();
                showSuccessMessage('Calendar refreshed successfully!');
            });

            function fetchCalendarEvents(start, end, successCallback, failureCallback) {
                // Show loading indicator
                document.getElementById('calendar-loading').classList.remove('hidden');
                
                const params = new URLSearchParams({
                    start: start,
                    end: end,
                    ...currentFilters
                });

                fetch(`{{ route('admin.calendar.events') }}?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                        // Update statistics when calendar events change
                        loadCalendarStats();
                        // Hide loading indicator
                        document.getElementById('calendar-loading').classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching calendar events:', error);
                        failureCallback(error);
                        // Hide loading indicator
                        document.getElementById('calendar-loading').classList.add('hidden');
                        // Show error message
                        showErrorMessage('Failed to load calendar events. Please try again.');
                    });
            }

            function loadCalendarStats() {
                const currentDate = new Date();
                const month = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0');
                
                fetch(`{{ route('admin.calendar.stats') }}?month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('total-employees').textContent = data.total_employees;
                        document.getElementById('present-count').textContent = data.present_count;
                        document.getElementById('absent-count').textContent = data.absent_count;
                        document.getElementById('shift-changes').textContent = data.shift_changes;
                        document.getElementById('pending-requests').textContent = data.pending_requests;
                    })
                    .catch(error => {
                        console.error('Error loading calendar stats:', error);
                        showErrorMessage('Failed to load calendar statistics.');
                    });
            }

            function showErrorMessage(message) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = 'toast toast-top toast-end';
                toast.innerHTML = `
                    <div class="alert alert-error">
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Remove toast after 5 seconds
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }

            function showSuccessMessage(message) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = 'toast toast-top toast-end';
                toast.innerHTML = `
                    <div class="alert alert-success">
                        <span>${message}</span>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Remove toast after 5 seconds
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }

            function showEventDetails(event) {
                const modal = document.getElementById('event-modal');
                const title = document.getElementById('modal-title');
                const content = document.getElementById('modal-content');
                
                title.textContent = event.title;
                
                const props = event.extendedProps;
                let contentHtml = '';
                
                switch(props.type) {
                    case 'attendance':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Date:</strong> ${event.startStr}</p>
                                ${props.clock_in ? `<p><strong>Clock In:</strong> ${props.clock_in}</p>` : ''}
                                ${props.clock_out ? `<p><strong>Clock Out:</strong> ${props.clock_out}</p>` : ''}
                            </div>
                        `;
                        break;
                    case 'shift':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Date:</strong> ${event.startStr}</p>
                                <p><strong>Shift:</strong> ${props.shift}</p>
                                ${props.reason ? `<p><strong>Reason:</strong> ${props.reason}</p>` : ''}
                            </div>
                        `;
                        break;
                    case 'absent':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Date:</strong> ${event.startStr}</p>
                                <p><strong>Shift:</strong> ${props.shift}</p>
                                ${props.reason ? `<p><strong>Reason:</strong> ${props.reason}</p>` : ''}
                            </div>
                        `;
                        break;
                    case 'pending_shift':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Date:</strong> ${event.startStr}</p>
                                <p><strong>Shift:</strong> ${props.shift}</p>
                                <p><strong>Status:</strong> <span class="badge badge-warning">Pending</span></p>
                                ${props.reason ? `<p><strong>Reason:</strong> ${props.reason}</p>` : ''}
                            </div>
                        `;
                        break;
                    case 'pending_absent':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Date:</strong> ${event.startStr}</p>
                                <p><strong>Shift:</strong> ${props.shift}</p>
                                <p><strong>Status:</strong> <span class="badge badge-warning">Pending</span></p>
                                ${props.reason ? `<p><strong>Reason:</strong> ${props.reason}</p>` : ''}
                            </div>
                        `;
                        break;
                }
                
                content.innerHTML = contentHtml;
                modal.classList.add('modal-open');
            }

            window.closeEventModal = function() {
                document.getElementById('event-modal').classList.remove('modal-open');
            };

            window.closeHelpModal = function() {
                document.getElementById('help-modal').classList.remove('modal-open');
            };

            // Export and Print functionality
            document.getElementById('help-btn').addEventListener('click', function() {
                document.getElementById('help-modal').classList.add('modal-open');
            });

            document.getElementById('export-btn').addEventListener('click', function() {
                exportCalendar();
            });

            document.getElementById('print-btn').addEventListener('click', function() {
                printCalendar();
            });

            function exportCalendar() {
                const currentView = calendar.view.type;
                const currentDate = calendar.getDate();
                
                // Create export data
                const exportData = {
                    view: currentView,
                    date: currentDate.toISOString(),
                    filters: currentFilters,
                    events: calendar.getEvents().map(event => ({
                        title: event.title,
                        start: event.startStr,
                        end: event.endStr,
                        type: event.extendedProps.type,
                        employee: event.extendedProps.employee
                    }))
                };
                
                // Download as JSON file
                const dataStr = JSON.stringify(exportData, null, 2);
                const dataBlob = new Blob([dataStr], {type: 'application/json'});
                const url = URL.createObjectURL(dataBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `calendar-export-${new Date().toISOString().split('T')[0]}.json`;
                link.click();
                URL.revokeObjectURL(url);
            }

            function printCalendar() {
                const printWindow = window.open('', '_blank');
                const calendarClone = calendarEl.cloneNode(true);
                
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Calendar Print View</title>
                            <style>
                                body { font-family: Arial, sans-serif; }
                                .fc { font-size: 12px; }
                                .fc-event { page-break-inside: avoid; }
                            </style>
                        </head>
                        <body>
                            <h1>Attendance Calendar</h1>
                            <div id="print-calendar"></div>
                        </body>
                    </html>
                `);
                
                printWindow.document.close();
                printWindow.print();
            }
        });
    </script>
</body>
</html>


