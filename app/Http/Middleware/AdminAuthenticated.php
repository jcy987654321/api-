<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        // Check if account is locked
        if ($user->isAccountLocked()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Your account is locked. Please try again later.']);
        }

        // Check if user is still active
        if ($user->status !== 'active') {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Your account is no longer active.']);
        }

        // Check session timeout
        $timeout = config('auth.admin_session.timeout');
        $lastActivity = $request->session()->get('last_activity');

        if ($lastActivity && (time() - $lastActivity > $timeout)) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'Your session has expired. Please login again.']);
        }

        $request->session()->put('last_activity', time());

        return $next($request);
    }
}
