<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'code' => 'AUTH_ERROR'
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Please login to access this page');
        }

        $userId = session('user_id');
        $user = \App\Models\User::find($userId);

        if (!$user || $user->status !== 'active' || !$user->is_active) {
            session()->forget('user_id');
            session()->forget('user_name');
            session()->forget('user_email');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not active',
                    'code' => 'ACCOUNT_INACTIVE'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account is not active');
        }

        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
