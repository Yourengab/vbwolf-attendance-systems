<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login');
        }
        return $next($request);
    }
}


