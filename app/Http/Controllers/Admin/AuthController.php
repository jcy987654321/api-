<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use App\Services\EncryptionService;
use App\Services\LoginLogService;
use App\Services\LoginThrottleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private EncryptionService $encryptionService,
        private LoginThrottleService $throttleService,
        private LoginLogService $loginLogService,
    ) {}

    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        $publicKey = session('rsa_public_key');
        if (!$publicKey) {
            $keys = $this->encryptionService->generateRsaKeyPair();
            session(['rsa_public_key' => $keys['public_key']]);
            session(['rsa_private_key' => $keys['private_key']]);
            $publicKey = $keys['public_key'];
        }

        return view('admin.login', ['publicKey' => $publicKey]);
    }

    public function login(LoginRequest $request)
    {
        $throttleCheck = $this->throttleService->checkThrottle($request);
        if (!$throttleCheck['allowed']) {
            $this->loginLogService->recordBlockedLogin($request, $throttleCheck['reason']);
            return back()
                ->withErrors(['email' => $throttleCheck['reason']])
                ->withInput();
        }

        $email = $request->validated('email');
        $password = $request->validated('password');
        $rememberMe = $request->validated('remember') ?? false;

        $user = User::where('email', $email)
            ->where('is_admin', true)
            ->first();

        if (!$user) {
            $this->throttleService->recordFailure($request);
            $this->loginLogService->recordBlockedLogin($request, 'Invalid credentials');
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput($request->only('email'));
        }

        if ($user->isAccountLocked()) {
            $this->loginLogService->recordBlockedLogin($request, 'Account locked');
            return back()
                ->withErrors(['email' => 'Your account is temporarily locked. Please try again later.'])
                ->withInput($request->only('email'));
        }

        if (!$this->encryptionService->verifyPassword($password, $user->password)) {
            $user->incrementFailedAttempts();

            if ($user->failed_login_attempts >= config('auth.throttle.max_attempts')) {
                $user->lockAccount(config('auth.throttle.lockout_duration'));
            }

            $this->throttleService->recordFailure($request, $user);
            $this->loginLogService->recordLoginAttempt(
                $request,
                $user,
                'failure',
                'Invalid password'
            );

            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->withInput($request->only('email'));
        }

        // Successful login
        $user->resetFailedAttempts();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $this->getClientIp($request),
        ]);

        $this->throttleService->recordSuccess($request, $user);
        $this->loginLogService->recordLoginAttempt($request, $user, 'success');

        Auth::guard('admin')->login($user, $rememberMe);

        session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function getPublicKey()
    {
        $publicKey = session('rsa_public_key');
        if (!$publicKey) {
            $keys = $this->encryptionService->generateRsaKeyPair();
            session(['rsa_public_key' => $keys['public_key']]);
            session(['rsa_private_key' => $keys['private_key']]);
            $publicKey = $keys['public_key'];
        }

        return response()->json(['public_key' => $publicKey]);
    }

    private function getClientIp(Request $request): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        return $ip;
    }
}
