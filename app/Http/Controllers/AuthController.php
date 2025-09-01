<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'admin') {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }

            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        if ($type === 'employee') {
            $validated = $request->validate([
                'nip' => ['required', 'string'],
            ]);

            $nip = strtoupper(trim($validated['nip']));

            $employee = \App\Models\Employee::with('user')
                ->whereRaw('UPPER(nip) = ?', [$nip])
                ->where('employment_status', 'active')
                ->first();

            if (!$employee || !$employee->user || $employee->user->role !== 'employee') {
                return back()->withErrors([
                    'nip' => 'NIP not found or inactive.',
                ])->onlyInput('nip');
            }

            Auth::login($employee->user, true);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'type' => 'Please select a login type.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function dashboard()
    {
        $user = Auth::user();
        if ($user->role === 'employee') {
            return redirect()->route('employee.dashboard');
        }
        // Redirect admins to the new admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard', compact('user'));
    }
}
