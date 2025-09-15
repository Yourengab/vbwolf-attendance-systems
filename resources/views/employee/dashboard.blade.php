@extends('layouts.main')

@section('title', 'Employee Dashboard')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-green-800">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Today</h2>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p>Date: <span id="today_date" class="font-medium text-gray-900">{{ now()->format('j F Y') }}</span></p>
                    <p>Time: <span id="now_time" class="font-medium text-gray-900">--:--</span></p>
                    <p>Clock In: <span class="font-medium text-gray-900">{{ $todayAttendance->clock_in ? \Carbon\Carbon::parse($todayAttendance->clock_in)->format('j F Y, g:i A') : '-' }}</span></p>
                    <p>Clock Out: <span class="font-medium text-gray-900">{{ $todayAttendance->clock_out ? \Carbon\Carbon::parse($todayAttendance->clock_out)->format('j F Y, g:i A') : '-' }}</span></p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors {{ $todayAttendance->clock_in ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $todayAttendance->clock_in ? 'disabled' : '' }} onclick="openClockInModal()">Clock In</button>
                    <form method="POST" action="{{ route('employee.clock_out') }}">
                        @csrf
                        <button class="px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-500 transition-colors {{ (!$todayAttendance->clock_in || $todayAttendance->clock_out) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ (!$todayAttendance->clock_in || $todayAttendance->clock_out) ? 'disabled' : '' }}>Clock Out</button>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Leave (Permission)</h2>
                @php
                    $activeLeave = $leaves->firstWhere('end_time', null);
                    $hasLeaveAny = $leaves->count() > 0;
                    $clockedIn = (bool) $todayAttendance->clock_in;
                    $clockedOut = (bool) $todayAttendance->clock_out;
                @endphp
                <ul class="text-sm text-gray-600 mb-4 space-y-1">
                    @forelse($leaves as $leave)
                        <li>Start: <span class="font-medium text-gray-900">{{ $leave->start_time }}</span> {{ $leave->end_time ? ' | End: '.$leave->end_time : '(active)' }}</li>
                    @empty
                        <li>No leave today</li>
                    @endforelse
                </ul>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex flex-col gap-4">
                        <form method="POST" action="{{ route('employee.leave_start') }}" class="space-y-3">
                            @csrf
                            <button class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors {{ (!$clockedIn || $clockedOut || $hasLeaveAny) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ (!$clockedIn || $clockedOut || $hasLeaveAny) ? 'disabled' : '' }}>Start Leave</button>
                            <div>
                                <label for="leave_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" id="leave_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Enter reason for leave"></textarea>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('employee.leave_end') }}">
                            @csrf
                            <button class="w-full px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-500 transition-colors {{ $activeLeave ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $activeLeave ? '' : 'disabled' }}>End Leave</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Overtime</h2>
                @php $activeOvertime = $overtimes->firstWhere('end_time', null); @endphp
                <ul class="text-sm text-gray-600 mb-4 space-y-1">
                    @forelse($overtimes as $ot)
                        <li>Start: <span class="font-medium text-gray-900">{{ $ot->start_time }}</span> {{ $ot->end_time ? ' | End: '.$ot->end_time : '(active)' }}</li>
                    @empty
                        <li>No overtime records</li>
                    @endforelse
                </ul>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex flex-col gap-4">
                        {{-- <form method="POST" action="{{ route('employee.overtime_start') }}" class="space-y-3">
                            @csrf
                            <button class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors {{ ($clockedOut && !$activeOvertime && $overtimes->count() == 0) ? '' : 'opacity-50 cursor-not-allowed' }}" {{ ($clockedOut && !$activeOvertime && $overtimes->count() == 0) ? '' : 'disabled' }}>Start Overtime</button>
                            <div>
                                <label for="overtime_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" id="overtime_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Enter reason for overtime"></textarea>
                            </div>
                        </form> --}}
                        {{-- <form method="POST" action="{{ route('employee.overtime_end') }}">
                            @csrf
                            <button class="w-full px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-500 transition-colors {{ $activeOvertime ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $activeOvertime ? '' : 'disabled' }}>End Overtime</button>
                        </form> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Daily Recap</h2>
            <div class="grid grid-cols-3 gap-6 text-center">
                <div>
                    <div class="text-sm text-gray-500 mb-1">Work</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $recap['work'] }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Leave</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $recap['leave'] }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Overtime</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $recap['overtime'] }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Request Day-Off</h2>
                <form method="POST" action="{{ route('employee.request_dayoff') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="dayoff_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <div class="relative">
                            <input type="text" id="dayoff_date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400 cursor-pointer" readonly required onclick="openDatePicker('dayoff_date')" placeholder="Click to select date" />
                            <button type="button" onclick="openDatePicker('dayoff_date')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="dayoff_shift" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                        <select id="dayoff_shift" name="shift" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                            @foreach($shiftHours as $sh)
                            <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dayoff_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea id="dayoff_reason" name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Optional"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Submit Request</button>
                </form>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Request Shift Change</h2>
                <form method="POST" action="{{ route('employee.request_shift') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="actual_date" class="block text-sm font-medium text-gray-700 mb-1">Actual Date</label>
                            <div class="relative">
                                <input type="text" id="actual_date" name="actual_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400 cursor-pointer" readonly required onclick="openDatePicker('actual_date')" placeholder="Click to select date" />
                                <button type="button" onclick="openDatePicker('actual_date')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="request_date" class="block text-sm font-medium text-gray-700 mb-1">Request Date</label>
                            <div class="relative">
                                <input type="text" id="request_date" name="request_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400 cursor-pointer" readonly required onclick="openDatePicker('request_date')" placeholder="Click to select date" />
                                <button type="button" onclick="openDatePicker('request_date')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="shift_change" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                        <select id="shift_change" name="shift" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                            @foreach($shiftHours as $sh)
                            <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="shift_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea id="shift_reason" name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" placeholder="Optional"></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Submit Request</button>
                </form>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Shift Schedule</h2>
            <form method="POST" action="{{ route('employee.shift-schedule') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="status" value="pending">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <div class="relative">
                            <input type="text" id="schedule_date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400 cursor-pointer" readonly required onclick="openDatePicker('schedule_date')" placeholder="Click to select date" />
                            <button type="button" onclick="openDatePicker('schedule_date')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="shift_hour" class="block text-sm font-medium text-gray-700 mb-1">Shift Hour</label>
                        <select id="shift_hour" name="shift_hour_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" required>
                            @foreach($shiftHours as $sh)
                            <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Submit Schedule</button>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">My Profile</h2>
                <div class="flex gap-3">
                    <button id="editProfileBtn" onclick="toggleProfileEdit()" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">
                        <i data-lucide="edit-2" class="w-4 h-4 inline mr-2"></i>
                        Edit Profile
                    </button>
                    <button id="saveProfileBtn" onclick="saveProfile()" class="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors hidden">
                        <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                        Save Changes
                    </button>
                    <button id="cancelProfileBtn" onclick="cancelProfileEdit()" class="px-4 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 transition-colors hidden">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Cancel
                    </button>
                </div>
            </div>

            <form id="profileForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="profile_name" name="name" value="{{ auth()->user()->name ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" readonly />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="profile_email" name="email" value="{{ auth()->user()->email ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-gray-400" readonly />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <input type="text" value="{{ auth()->user()->employee->nip ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                            <input type="text" value="{{ auth()->user()->employee->branch->name ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" value="{{ auth()->user()->employee->position->name ?? 'N/A' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly />
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800 mb-1">Profile Information</h4>
                            <p class="text-sm text-blue-700">You can update your name and email address. Employee ID, branch, and position are managed by your administrator.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Clock In Modal -->
    <div id="clockInModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeClockInModal()"></div>
        <div class="relative mx-auto my-8 max-w-md bg-white rounded-lg shadow-xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Clock In - Photo Proof</h3>
                <p class="text-sm text-gray-600 mb-4">Upload a photo or take one with your camera. When prompted, allow camera access.</p>
                <form method="POST" action="{{ route('employee.clock_in') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-4">
                        <div id="camera_wrap">
                            <video id="cam" autoplay playsinline class="w-full rounded-lg bg-gray-900"></video>
                        </div>
                        <canvas id="snapshot" class="hidden"></canvas>
                        <img id="preview" class="hidden w-full rounded-lg" alt="Preview" />
                        <input type="hidden" name="photo_data" id="photo_data" />
                        <div id="camera_error" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800"></div>
                        <div class="flex gap-3">
                            <button type="button" class="px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-500 transition-colors" id="start_cam">Start Camera</button>
                            <button type="button" class="px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors" id="take_photo">Take Photo</button>
                            <button type="button" class="px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-500 transition-colors hidden" id="retake">Retake</button>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 transition-colors">Submit</button>
                        <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors" onclick="closeClockInModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Date Picker Modal -->
    <div id="datePickerModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-600 bg-opacity-75" onclick="closeDatePicker()"></div>
        <div class="relative mx-auto my-8 max-w-sm bg-white rounded-lg shadow-xl">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <button onclick="changeMonth(-1)" class="p-1 text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-900" id="calendar-month-year"></h3>
                    <button onclick="changeMonth(1)" class="p-1 text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-7 gap-1 mb-4">
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Su</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Mo</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Tu</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">We</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Th</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Fr</div>
                    <div class="text-center text-sm font-medium text-gray-500 py-2">Sa</div>
                </div>
                <div class="grid grid-cols-7 gap-1" id="calendar-days"></div>
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="closeDatePicker()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Cancel</button>
                    <button onclick="selectToday()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Today</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const startBtn = document.getElementById('start_cam');
            const snapBtn = document.getElementById('take_photo');
            const video = document.getElementById('cam');
            const cameraWrap = document.getElementById('camera_wrap');
            const canvas = document.getElementById('snapshot');
            const photoData = document.getElementById('photo_data');
            const preview = document.getElementById('preview');
            const camError = document.getElementById('camera_error');
            const retakeBtn = document.getElementById('retake');
            let stream;

            async function requestCamera() {
                camError.classList.add('hidden');
                camError.textContent = '';
                if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
                    camError.textContent = 'Camera API not supported in this browser.';
                    camError.classList.remove('hidden');
                    return;
                }
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                    video.srcObject = stream;
                } catch (e) {
                    const msg = e && e.name === 'NotAllowedError'
                        ? 'Camera permission denied. Please allow access and try again.'
                        : e && e.name === 'NotFoundError'
                            ? 'No camera device found.'
                            : 'Unable to access camera.';
                    camError.textContent = msg;
                    camError.classList.remove('hidden');
                }
            }

            startBtn?.addEventListener('click', async () => {
                await requestCamera();
            });

            snapBtn?.addEventListener('click', () => {
                if (!video.videoWidth) return;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                photoData.value = dataUrl;
                preview.src = dataUrl;
                preview.classList.remove('hidden');
                cameraWrap.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                snapBtn.classList.add('hidden');
            });

            retakeBtn?.addEventListener('click', async () => {
                preview.classList.add('hidden');
                retakeBtn.classList.add('hidden');
                photoData.value = '';
                cameraWrap.classList.remove('hidden');
                await requestCamera();
                snapBtn.classList.remove('hidden');
            });

            // Modal functions
            window.openClockInModal = async function() {
                document.getElementById('clockInModal').classList.remove('hidden');
                await requestCamera();
            }

            window.closeClockInModal = function() {
                document.getElementById('clockInModal').classList.add('hidden');
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                    video.srcObject = null;
                }
            }
        })();

        // Date Picker functionality
        (function(){
            let currentDate = new Date();
            let selectedDate = null;
            let targetInputId = null;

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];

            window.openDatePicker = function(inputId) {
                targetInputId = inputId;
                const input = document.getElementById(inputId);
                if (input && input.value) {
                    // Parse the displayed date format (e.g., "September 9, 2025")
                    const dateParts = input.value.split(' ');
                    if (dateParts.length >= 3) {
                        const month = monthNames.indexOf(dateParts[0]);
                        const day = parseInt(dateParts[1].replace(',', ''));
                        const year = parseInt(dateParts[2]);
                        if (month >= 0 && !isNaN(day) && !isNaN(year)) {
                            selectedDate = new Date(year, month, day);
                            currentDate = new Date(selectedDate);
                        } else {
                            selectedDate = new Date();
                            currentDate = new Date();
                        }
                    } else {
                        selectedDate = new Date();
                        currentDate = new Date();
                    }
                } else {
                    selectedDate = new Date();
                    currentDate = new Date();
                }
                renderCalendar();
                document.getElementById('datePickerModal').classList.remove('hidden');
            }

            window.closeDatePicker = function() {
                document.getElementById('datePickerModal').classList.add('hidden');
                targetInputId = null;
                selectedDate = null;
            }

            window.changeMonth = function(delta) {
                currentDate.setMonth(currentDate.getMonth() + delta);
                renderCalendar();
            }

            window.selectToday = function() {
                selectedDate = new Date();
                currentDate = new Date(selectedDate);
                renderCalendar();
                setSelectedDate();
            }

            window.selectDate = function(day) {
                selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                setSelectedDate();
                closeDatePicker();
            }

            function setSelectedDate() {
                if (targetInputId && selectedDate) {
                    const input = document.getElementById(targetInputId);
                    if (input) {
                        // Display user-friendly format
                        const displayDate = selectedDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        input.value = displayDate;

                        // Set the actual form value in YYYY-MM-DD format
                        const year = selectedDate.getFullYear();
                        const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                        const day = String(selectedDate.getDate()).padStart(2, '0');
                        const actualValue = `${year}-${month}-${day}`;

                        // Create or update hidden input with the actual value
                        let hiddenInput = document.getElementById(targetInputId + '_hidden');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.id = targetInputId + '_hidden';
                            hiddenInput.name = input.name;
                            input.parentNode.insertBefore(hiddenInput, input.nextSibling);
                            // Change the original input name to avoid conflicts
                            input.name = '';
                        }
                        hiddenInput.value = actualValue;

                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            }

            function renderCalendar() {
                const monthYear = document.getElementById('calendar-month-year');
                const daysContainer = document.getElementById('calendar-days');

                monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());

                daysContainer.innerHTML = '';

                for (let i = 0; i < 42; i++) {
                    const day = new Date(startDate);
                    day.setDate(startDate.getDate() + i);

                    const dayElement = document.createElement('button');
                    dayElement.className = 'text-center py-2 text-sm hover:bg-gray-100 rounded-md transition-colors';
                    dayElement.textContent = day.getDate();

                    if (day.getMonth() !== currentDate.getMonth()) {
                        dayElement.classList.add('text-gray-400');
                    } else {
                        dayElement.classList.add('text-gray-900');
                    }

                    if (selectedDate &&
                        day.getDate() === selectedDate.getDate() &&
                        day.getMonth() === selectedDate.getMonth() &&
                        day.getFullYear() === selectedDate.getFullYear()) {
                        dayElement.classList.add('bg-gray-800', 'text-white', 'hover:bg-gray-700');
                    }

                    if (day.toDateString() === new Date().toDateString()) {
                        dayElement.classList.add('font-semibold');
                        if (!selectedDate || day.toDateString() !== selectedDate.toDateString()) {
                            dayElement.classList.add('ring-2', 'ring-gray-300');
                        }
                    }

                    dayElement.onclick = () => selectDate(day.getDate());
                    daysContainer.appendChild(dayElement);
                }
            }
        })();

        // Profile Edit functions
        (function(){
            let originalName = '';
            let originalEmail = '';

            window.toggleProfileEdit = function() {
                const nameInput = document.getElementById('profile_name');
                const emailInput = document.getElementById('profile_email');
                const editBtn = document.getElementById('editProfileBtn');
                const saveBtn = document.getElementById('saveProfileBtn');
                const cancelBtn = document.getElementById('cancelProfileBtn');

                // Store original values
                originalName = nameInput.value;
                originalEmail = emailInput.value;

                // Toggle readonly state
                nameInput.readOnly = !nameInput.readOnly;
                emailInput.readOnly = !emailInput.readOnly;

                // Toggle button visibility
                editBtn.classList.toggle('hidden');
                saveBtn.classList.toggle('hidden');
                cancelBtn.classList.toggle('hidden');

                // Focus on name input when editing starts
                if (!nameInput.readOnly) {
                    nameInput.focus();
                }
            }

            window.saveProfile = function() {
                const nameInput = document.getElementById('profile_name');
                const emailInput = document.getElementById('profile_email');
                const saveBtn = document.getElementById('saveProfileBtn');
                const newName = nameInput.value.trim();
                const newEmail = emailInput.value.trim();

                // Basic validation
                if (!newName) {
                    alert('Name cannot be empty.');
                    nameInput.focus();
                    return;
                }

                if (!newEmail) {
                    alert('Email cannot be empty.');
                    emailInput.focus();
                    return;
                }

                // Simple email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(newEmail)) {
                    alert('Please enter a valid email address.');
                    emailInput.focus();
                    return;
                }

                // Check if anything changed
                if (newName === originalName && newEmail === originalEmail) {
                    alert('No changes were made to your profile.');
                    toggleProfileEdit(); // Exit edit mode
                    return;
                }

                // Disable save button to prevent double submission
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';

                // Prepare form data
                const formData = new FormData();
                formData.append('name', newName);
                formData.append('email', newEmail);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Send AJAX request
                fetch('/employee/profile', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update original values
                        originalName = data.data.name;
                        originalEmail = data.data.email;

                        // Show success message
                        alert('Your profile has been updated successfully!\n\nName: ' + data.data.name + '\nEmail: ' + data.data.email);

                        // Exit edit mode
                        toggleProfileEdit();
                    } else {
                        alert('Error updating profile. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating profile. Please try again.');
                })
                .finally(() => {
                    // Re-enable save button
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i data-lucide="save" class="w-4 h-4 inline mr-2"></i>Save Changes';
                    lucide.createIcons(); // Re-create icons
                });
            }

            window.cancelProfileEdit = function() {
                const nameInput = document.getElementById('profile_name');
                const emailInput = document.getElementById('profile_email');

                // Restore original values
                nameInput.value = originalName;
                emailInput.value = originalEmail;

                // Exit edit mode
                toggleProfileEdit();
            }
        })();

        // Realtime clock
        (function(){
            const timeEl = document.getElementById('now_time');
            function pad(n){ return n < 10 ? '0'+n : n; }
            function tick(){
                const d = new Date();
                let h = d.getHours();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12; h = h ? h : 12;
                const str = h+':'+pad(d.getMinutes())+':'+pad(d.getSeconds())+' '+ampm;
                if (timeEl) timeEl.textContent = str;
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
