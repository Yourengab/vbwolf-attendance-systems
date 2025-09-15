@extends('layouts.main')

@section('title', 'Calendar')
@section('page-title', 'Attendance Calendar')

@section('head')
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
            background-color: #374151 !important;
            border-color: #374151 !important;
            color: white !important;
        }
        .fc-button:hover {
            background-color: #1f2937 !important;
            border-color: #1f2937 !important;
        }
        .fc-button-active {
            background-color: #111827 !important;
            border-color: #111827 !important;
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
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Total Employees</div>
            <div class="text-3xl font-bold text-gray-900" id="total-employees">-</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Present Today</div>
            <div class="text-3xl font-bold text-green-600" id="present-count">-</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Absent Today</div>
            <div class="text-3xl font-bold text-red-600" id="absent-count">-</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Shift Changes</div>
            <div class="text-3xl font-bold text-blue-600" id="shift-changes">-</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Pending Requests</div>
            <div class="text-3xl font-bold text-yellow-600" id="pending-requests">-</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-sm text-gray-500 mb-2">Shift Schedule</div>
            <div class="text-3xl font-bold text-purple-600" id="shift-schedule">-</div>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <div>
                <label for="employee-filter" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select id="employee-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="branch-filter" class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select id="branch-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="position-filter" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <select id="position-filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400">
                    <option value="">All Positions</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button id="filter-btn" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Filter</button>
                <button id="clear-filter-btn" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors">Clear</button>
                <button id="refresh-btn" class="px-4 py-2 bg-gray-100 text-gray-600 font-medium rounded-md hover:bg-gray-200 transition-colors">🔄</button>
            </div>
        </div>
        <div class="border border-gray-200 rounded-lg h-[70vh] overflow-hidden relative">
            <div id="calendar" class="h-full"></div>
            <div id="calendar-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
            </div>
        </div>
                
        <!-- Quick Actions -->
        <div class="mt-6 flex flex-wrap items-center justify-between">
            <div class="text-sm">
                <div class="flex flex-wrap gap-6">
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-green-500 mr-2"></span>Present</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-blue-500 mr-2"></span>Shift Change</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-red-500 mr-2"></span>Absent</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-yellow-500 mr-2"></span>Pending Requests</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-purple-500 mr-2"></span>Shift Schedule</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button id="help-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">?</button>
                <button id="export-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">Export Calendar</button>
                <button id="print-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">Print View</button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex flex-wrap items-center justify-between">
            <div class="text-sm">
                <div class="flex flex-wrap gap-6">
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-green-500 mr-2"></span>Present</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-blue-500 mr-2"></span>Shift Change</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-red-500 mr-2"></span>Absent</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-yellow-500 mr-2"></span>Pending Requests</span>
                    <span class="flex items-center"><span class="inline-block w-3 h-3 rounded bg-purple-500 mr-2"></span>Shift Schedule</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button id="help-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">?</button>
                <button id="export-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">Export Calendar</button>
                <button id="print-btn" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">Print View</button>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div id="event-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeEventModal()"></div>
        <div class="relative mx-auto my-8 max-w-md bg-white rounded-lg shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title"></h3>
            </div>
            <div class="px-6 py-4" id="modal-content"></div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors" onclick="closeEventModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="help-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeHelpModal()"></div>
        <div class="relative mx-auto my-8 max-w-lg bg-white rounded-lg shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Calendar Help & Keyboard Shortcuts</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <h4 class="font-semibold mb-2 text-gray-900">Navigation</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + ←</kbd> Previous period</div>
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + →</kbd> Next period</div>
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + Home</kbd> Go to today</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-2 text-gray-900">View Switching</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + 1</kbd> Month view</div>
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + 2</kbd> Week view</div>
                        <div><kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + 3</kbd> List view</div>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-2 text-gray-900">Legend</h4>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div><span class="inline-block w-3 h-3 rounded bg-green-500 mr-2"></span>Present - Employee attended work</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-blue-500 mr-2"></span>Shift Change - Approved shift change request</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-red-500 mr-2"></span>Absent - Approved absence request</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-yellow-500 mr-2"></span>Pending - Requests awaiting approval</div>
                        <div><span class="inline-block w-3 h-3 rounded bg-purple-500 mr-2"></span>Shift Schedule - Scheduled shifts</div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors" onclick="closeHelpModal()">Close</button>
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
                        case 'present':
                            info.el.style.backgroundColor = '#10b981'; // Green
                            info.el.style.borderColor = '#10b981';
                            break;
                        case 'absent':
                            info.el.style.backgroundColor = '#ef4444'; // Red
                            info.el.style.borderColor = '#ef4444';
                            break;
                        case 'shift_change':
                            info.el.style.backgroundColor = '#3b82f6'; // Blue
                            info.el.style.borderColor = '#3b82f6';
                            break;
                        case 'pending_shift':
                        case 'pending_absent':
                            info.el.style.backgroundColor = '#f59e0b'; // Orange
                            info.el.style.borderColor = '#f59e0b';
                            break;
                        case 'shift_schedule':
                            info.el.style.backgroundColor = '#2332db'; // Custom Blue
                            info.el.style.borderColor = '#2332db';
                            break;
                        default:
                            info.el.style.backgroundColor = '#6b7280'; // Gray
                            info.el.style.borderColor = '#6b7280';
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
                        document.getElementById('shift-schedule').textContent = data.shift_schedule;
                    })
                    .catch(error => {
                        console.error('Error loading calendar stats:', error);
                        showErrorMessage('Failed to load calendar statistics.');
                    });
            }

            function showErrorMessage(message) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 max-w-sm';
                toast.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 text-sm font-medium text-red-800">
                            ${message}
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 text-red-400 hover:bg-red-100 hover:text-red-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);

                // Remove toast after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
            }

            function showSuccessMessage(message) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 max-w-sm';
                toast.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 text-sm font-medium text-green-800">
                            ${message}
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex rounded-md p-1.5 text-green-400 hover:bg-green-100 hover:text-green-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);

                // Remove toast after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
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
                        case 'shift_schedule':
                        contentHtml = `
                            <div class="space-y-2">
                                <p><strong>Employee:</strong> ${props.employee}</p>
                                <p><strong>Shift Hour:</strong> ${props.shift_hour}</p>
                            </div>
                        `;
                        break;
                }
                
                content.innerHTML = contentHtml;
                modal.classList.remove('hidden');
            }

            window.closeEventModal = function() {
                document.getElementById('event-modal').classList.add('hidden');
            };

            window.closeHelpModal = function() {
                document.getElementById('help-modal').classList.add('hidden');
            };

            // Export and Print functionality
            document.getElementById('help-btn').addEventListener('click', function() {
                document.getElementById('help-modal').classList.remove('hidden');
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
@endsection
