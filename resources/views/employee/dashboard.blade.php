<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <div class="navbar bg-base-100 shadow">
        <div class="flex-1 px-2">Employee Dashboard</div>
        <div class="flex-none">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-ghost">Logout</button>
            </form>
        </div>
    </div>

    <div class="container mx-auto p-4 space-y-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-4">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Today</h2>
                    <p>Date: <span id="today_date">{{ now()->format('j F Y') }}</span></p>
                    <p>Time: <span id="now_time">--:--</span></p>
                    <p>Clock In: {{ $todayAttendance->clock_in ? \Carbon\Carbon::parse($todayAttendance->clock_in)->format('j F Y, g:i A') : '-' }}</p>
                    <p>Clock Out: {{ $todayAttendance->clock_out ? \Carbon\Carbon::parse($todayAttendance->clock_out)->format('j F Y, g:i A') : '-' }}</p>
                    <div class="divider"></div>
                    <div class="flex flex-wrap gap-2">
                        <button class="btn btn-primary" {{ $todayAttendance->clock_in ? 'disabled' : '' }} onclick="openClockInModal()">Clock In</button>
                        <form method="POST" action="{{ route('employee.clock_out') }}">
                            @csrf
                            <button class="btn btn-secondary" {{ (!$todayAttendance->clock_in || $todayAttendance->clock_out) ? 'disabled' : '' }}>Clock Out</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Leave (Permission)</h2>
                    @php
                        $activeLeave = $leaves->firstWhere('end_time', null);
                        $hasLeaveAny = $leaves->count() > 0;
                        $clockedIn = (bool) $todayAttendance->clock_in;
                        $clockedOut = (bool) $todayAttendance->clock_out;
                    @endphp
                    <ul class="text-sm">
                        @forelse($leaves as $leave)
                            <li>Start: {{ $leave->start_time }} {{ $leave->end_time ? ' | End: '.$leave->end_time : '(active)' }}</li>
                        @empty
                            <li>No leave today</li>
                        @endforelse
                    </ul>
                    <div class="divider"></div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('employee.leave_start') }}" class="flex flex-col gap-2">
                            @csrf
                            <button class="btn" {{ (!$clockedIn || $clockedOut || $hasLeaveAny) ? 'disabled' : '' }}>Start Leave</button>
                            <label for="reason">Reason</label>
                            <textarea name="reason" id="reason" cols="30" rows="10" class="border border-black"></textarea>
                        </form>
                        <form method="POST" action="{{ route('employee.leave_end') }}">
                            @csrf
                            <button class="btn" {{ $activeLeave ? '' : 'disabled' }}>End Leave</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Overtime</h2>
                    @php $activeOvertime = $overtimes->firstWhere('end_time', null); @endphp
                    <ul class="text-sm">
                        @forelse($overtimes as $ot)
                            <li>Start: {{ $ot->start_time }} {{ $ot->end_time ? ' | End: '.$ot->end_time : '(active)' }}</li>
                        @empty
                            <li>No overtime records</li>
                        @endforelse
                    </ul>
                    <div class="divider"></div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('employee.overtime_start') }}">
                            @csrf
                            <button class="btn" {{ ($clockedOut && !$activeOvertime && $overtimes->count() == 0) ? '' : 'disabled' }}>Start Overtime</button>
                             <label for="reason">Reason</label>
                            <textarea name="reason" id="reason" cols="30" rows="10" class="border border-black"></textarea>
                        </form>
                        <form method="POST" action="{{ route('employee.overtime_end') }}">
                            @csrf
                            <button class="btn" {{ $activeOvertime ? '' : 'disabled' }}>End Overtime</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">Daily Recap</h2>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-sm opacity-70">Work</div>
                        <div class="text-lg font-semibold">{{ $recap['work'] }}</div>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Leave</div>
                        <div class="text-lg font-semibold">{{ $recap['leave'] }}</div>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Overtime</div>
                        <div class="text-lg font-semibold">{{ $recap['overtime'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Request Day-Off</h2>
                    <form method="POST" action="{{ route('employee.request_dayoff') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                        @csrf
                        <div class="form-control">
                            <label class="label"><span class="label-text">Date</span></label>
                            <input type="date" name="date" class="input input-bordered" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Shift</span></label>
                            <select name="shift" class="select select-bordered" required>
                                <option value="morning">Morning</option>
                                <option value="evening">Evening</option>
                                <option value="night">Night</option>
                            </select>
                        </div>
                        <div class="form-control md:col-span-3">
                            <label class="label"><span class="label-text">Reason</span></label>
                            <textarea name="reason" class="textarea textarea-bordered" placeholder="Optional"></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Request Shift Change</h2>
                    <form method="POST" action="{{ route('employee.request_shift') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                        @csrf
                        <div class="form-control">
                            <label class="label"><span class="label-text">Actual Date</span></label>
                            <input type="date" name="actual_date" class="input input-bordered" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Request Date</span></label>
                            <input type="date" name="request_date" class="input input-bordered" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Shift</span></label>
                            <select name="shift" class="select select-bordered" required>
                                <option value="morning">Morning</option>
                                <option value="evening">Evening</option>
                                <option value="night">Night</option>
                            </select>
                        </div>
                        <div class="form-control md:col-span-3">
                            <label class="label"><span class="label-text">Reason</span></label>
                            <textarea name="reason" class="textarea textarea-bordered" placeholder="Optional"></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
<div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title">Shift Schedule</h2>
                    <form method="POST" action="{{ route('employee.shift-schedule') }}" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                        @csrf
                        <div class="form-control">
                            <label class="label"><span class="label-text">Date</span></label>
                            <input type="date" name="date" class="input input-bordered" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Shift Hour</span></label>
                            <select name="shift_hour_id" class="select select-bordered" required>
                                @foreach($shiftHours as $sh)
                                <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="status" value="pending">
                        <div class="md:col-span-3">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <dialog id="clockInModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-2">Clock In - Photo Proof</h3>
            <p class="text-sm opacity-70 mb-2">Upload a photo or take one with your camera. When prompted, allow camera access.</p>
            <form method="POST" action="{{ route('employee.clock_in') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="divider">or</div>
                <div class="space-y-2">
                    <div id="camera_wrap">
                        <video id="cam" autoplay playsinline class="w-full rounded bg-black"></video>
                    </div>
                    <canvas id="snapshot" class="hidden"></canvas>
                    <img id="preview" class="hidden w-full rounded" alt="Preview" />
                    <input type="hidden" name="photo_data" id="photo_data" />
                    <div id="camera_error" class="hidden alert alert-error text-sm"></div>
                    <div class="flex gap-2">
                        <button type="button" class="btn" id="start_cam">Start Camera</button>
                        <button type="button" class="btn" id="take_photo">Take Photo</button>
                        <button type="button" class="btn hidden" id="retake">Retake</button>
                    </div>
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn" onclick="clockInModal.close()">Cancel</button>
                </div>
            </form>
        </div>
    </dialog>
    <script>
        (function(){
            const startBtn = document.getElementById('start_cam');
            const snapBtn = document.getElementById('take_photo');
            const video = document.getElementById('cam');
            const cameraWrap = document.getElementById('camera_wrap');
            const canvas = document.getElementById('snapshot');
            const photoData = document.getElementById('photo_data');
            const fileInput = document.getElementById('photo_file');
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
                // hide take photo after capture
                snapBtn.classList.add('hidden');
            });
            fileInput?.addEventListener('change', () => {
                const file = fileInput.files && fileInput.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    photoData.value = '';
                    cameraWrap.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');
                    // hide take photo when using uploaded file
                    snapBtn.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
            retakeBtn?.addEventListener('click', async () => {
                preview.classList.add('hidden');
                retakeBtn.classList.add('hidden');
                photoData.value = '';
                cameraWrap.classList.remove('hidden');
                await requestCamera();
                // show take photo again and clear file selection
                snapBtn.classList.remove('hidden');
                if (fileInput) fileInput.value = '';
            });
            // Stop camera when modal closes (best-effort)
            window.clockInModal?.addEventListener('close', () => {
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                    video.srcObject = null;
                }
            });
            // expose to onclick
            window.openClockInModal = async function() {
                clockInModal.showModal();
                // proactively ask for permission when modal opens
                await requestCamera();
            }
        })();
        // realtime clock
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
</body>
</html>


