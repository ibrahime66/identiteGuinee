<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()?->role !== 'admin') {
            Log::warning('AUTH admin middleware denied', [
                'path' => $request->path(),
                'auth_check' => Auth::check(),
                'user_id' => Auth::id(),
                'role' => Auth::user()?->role,
                'session_id' => $request->session()->getId(),
            ]);
            return redirect()->route('login');
        }
        return $next($request);
    }
}
