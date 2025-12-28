<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (! Auth::guard('admin')->check()) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('admin.login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('admin')->user();

        if (! $user->isAdmin() || ! $user->isActive()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
