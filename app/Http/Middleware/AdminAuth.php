<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin
        // This is a placeholder - actual authentication logic will be implemented
        if (! $request->user() || ! $request->user()->isAdmin) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
