<!-- filepath: c:\laragon\www\vbwolf-attendance-system\resources\views\auth\login.blade.php -->
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-base-100 shadow-lg rounded-xl overflow-hidden">
            <div class="px-8 py-10">
                <div class="flex flex-col items-center mb-8">
                    <svg class="w-12 h-12 text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900">VB Wolf Attendance</h2>
                </div>
                @if ($errors->any())
                    <div class="alert alert-error text-sm mb-4">
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" id="login-type" value="{{ old('type', 'employee') }}" />
                    <div class="flex gap-2 mb-4">
                        <button type="button" id="btn-employee" class="btn flex-1">Employee</button>
                        <button type="button" id="btn-admin" class="btn flex-1">Admin</button>
                    </div>
                    @error('type')
                    <div class="text-error text-sm mb-2">{{ $message }}</div>
                    @enderror

                    <div id="admin-fields">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" placeholder="you@example.com" />
                            @error('email')
                            <div class="text-error text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Password</label>
                            <input type="password" name="password" class="input input-bordered w-full" placeholder="••••••••" />
                        </div>
                    </div>

                    <div id="employee-fields" class="hidden">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" class="input input-bordered w-full" placeholder="EMP-001" />
                            @error('nip')
                            <div class="text-error text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                        <span class="text-sm">Remember me</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-full mt-2">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const inputType = document.getElementById('login-type');
            const btnEmp = document.getElementById('btn-employee');
            const btnAdm = document.getElementById('btn-admin');
            const adminFields = document.getElementById('admin-fields');
            const employeeFields = document.getElementById('employee-fields');
            const email = document.querySelector('input[name="email"]');
            const password = document.querySelector('input[name="password"]');
            const nip = document.querySelector('input[name="nip"]');
            const form = document.querySelector('form');
            function render() {
                const type = inputType.value;
                if (type === 'employee') {
                    adminFields.classList.add('hidden');
                    employeeFields.classList.remove('hidden');
                    btnEmp.classList.add('btn-primary');
                    btnEmp.classList.remove('btn-outline');
                    btnAdm.classList.add('btn-outline');
                    btnAdm.classList.remove('btn-primary');
                    if (email) email.removeAttribute('required');
                    if (password) password.removeAttribute('required');
                    if (nip) nip.setAttribute('required', 'required');
                    if (email) email.setAttribute('disabled', 'disabled');
                    if (password) password.setAttribute('disabled', 'disabled');
                    if (nip) nip.removeAttribute('disabled');
                } else {
                    employeeFields.classList.add('hidden');
                    adminFields.classList.remove('hidden');
                    btnAdm.classList.add('btn-primary');
                    btnAdm.classList.remove('btn-outline');
                    btnEmp.classList.add('btn-outline');
                    btnEmp.classList.remove('btn-primary');
                    if (email) email.setAttribute('required', 'required');
                    if (password) password.setAttribute('required', 'required');
                    if (nip) nip.removeAttribute('required');
                    if (email) email.removeAttribute('disabled');
                    if (password) password.removeAttribute('disabled');
                    if (nip) nip.setAttribute('disabled', 'disabled');
                }
            }
            btnEmp.addEventListener('click', () => { inputType.value = 'employee'; render(); });
            btnAdm.addEventListener('click', () => { inputType.value = 'admin'; render(); });
            form.addEventListener('submit', () => {
                inputType.value = inputType.value === 'admin' ? 'admin' : 'employee';
            });
            btnEmp.classList.add('btn-outline');
            btnAdm.classList.add('btn-outline');
            render();
        })();
    </script>
</body>
</html>