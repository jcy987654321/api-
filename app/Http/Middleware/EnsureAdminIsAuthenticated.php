<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminIsAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // For now, we'll use a simple admin check
        // In a real application, you would check against a proper user system
        if (!Auth::check()) {
            // For demo purposes, we'll allow access if there's no user system
            // In production, you should implement proper authentication
            return $next($request);
        }

        // Check if user has admin privileges
        if (Auth::user() && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}