<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title justify-center">Sign in</h2>
                    @if ($errors->any())
                        <div class="alert alert-error text-sm">
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" id="login-type" value="{{ old('type', 'employee') }}" />
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="btn-employee" class="btn">Employee</button>
                            <button type="button" id="btn-admin" class="btn">Admin</button>
                        </div>
                        @error('type')
                        <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <div id="admin-fields">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Email</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="input input-bordered" placeholder="you@example.com" />
                                @error('email')
                                <div class="text-error text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Password</span></label>
                                <input type="password" name="password" class="input input-bordered" placeholder="••••••••" />
                            </div>
                        </div>

                        <div id="employee-fields" class="hidden">
                            <div class="form-control">
                                <label class="label"><span class="label-text">NIP</span></label>
                                <input type="text" name="nip" value="{{ old('nip') }}" class="input input-bordered" placeholder="EMP-001" />
                                @error('nip')
                                <div class="text-error text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                                <span class="label-text">Remember me</span>
                            </label>
                        </div>
                        <div class="form-control mt-2">
                            <button type="submit" class="btn btn-primary w-full">Login</button>
                        </div>
                    </form>
                </div>
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
                // ensure hidden input reflects current mode before submit
                inputType.value = inputType.value === 'admin' ? 'admin' : 'employee';
            });
            btnEmp.classList.add('btn-outline');
            btnAdm.classList.add('btn-outline');
            render();
        })();
    </script>
</body>
</html>


