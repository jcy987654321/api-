<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\LoginHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly LoginHistoryService $loginHistoryService
    ) {
    }

    public function login(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function showLoginForm(): View|RedirectResponse
    {
        return $this->login();
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $user = $this->authService->login(
            (string) $request->input('email'),
            (string) $request->input('password'),
            (bool) $request->input('remember', false)
        );

        if (! $user) {
            Log::channel('security')->warning('Failed admin login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        $this->loginHistoryService->recordLogin(
            $user->id,
            (string) $request->ip(),
            $request->userAgent()
        );

        Log::channel('security')->info('Admin login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('admin')->user();

        if ($user) {
            $this->loginHistoryService->recordLogout(
                $user->id,
                (string) $request->ip(),
                $request->userAgent()
            );

            Log::channel('security')->info('Admin logout', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
