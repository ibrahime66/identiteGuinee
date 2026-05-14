<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()?->role !== 'citizen') {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
