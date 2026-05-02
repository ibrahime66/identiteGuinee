<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CitizenAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('citizen_authenticated')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
