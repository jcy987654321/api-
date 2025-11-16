<?php

namespace App\Services;

use App\Models\LoginThrottle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginThrottleService
{
    private int $maxAttempts = 5;
    private int $lockoutDuration = 30; // minutes
    private int $resetWindow = 60; // minutes

    public function __construct()
    {
        $this->maxAttempts = config('auth.throttle.max_attempts', 5);
        $this->lockoutDuration = config('auth.throttle.lockout_duration', 30);
        $this->resetWindow = config('auth.throttle.reset_window', 60);
    }

    public function checkThrottle(Request $request, ?User $user = null): array
    {
        $ip = $this->getClientIp($request);
        $throttle = LoginThrottle::firstWhere('ip_address', $ip);

        if (!$throttle) {
            $throttle = LoginThrottle::create([
                'ip_address' => $ip,
                'user_id' => $user?->id,
                'email' => $user?->email,
            ]);
        }

        // Check if currently locked
        if ($throttle->isLocked()) {
            return [
                'allowed' => false,
                'reason' => 'Account temporarily locked due to too many failed attempts',
                'locked_until' => $throttle->locked_until,
            ];
        }

        // Check if should reset attempts (window passed)
        if ($throttle->first_attempt_at && $throttle->first_attempt_at->addMinutes($this->resetWindow)->isPast()) {
            $throttle->resetAttempts();
            return ['allowed' => true];
        }

        return ['allowed' => true];
    }

    public function recordFailure(Request $request, ?User $user = null): void
    {
        $ip = $this->getClientIp($request);
        $throttle = LoginThrottle::getOrCreateForIp($ip, $user?->id, $user?->email);

        // If new attempt window, reset counter
        if (!$throttle->first_attempt_at || $throttle->first_attempt_at->addMinutes($this->resetWindow)->isPast()) {
            $throttle->update([
                'failed_attempts' => 1,
                'first_attempt_at' => now(),
                'last_attempt_at' => now(),
            ]);
        } else {
            $throttle->recordFailure();

            // Lock if max attempts reached
            if ($throttle->failed_attempts >= $this->maxAttempts) {
                $throttle->update([
                    'is_locked' => true,
                    'locked_until' => now()->addMinutes($this->lockoutDuration),
                ]);
            }
        }
    }

    public function recordSuccess(Request $request, User $user): void
    {
        $ip = $this->getClientIp($request);
        $throttle = LoginThrottle::firstWhere(['ip_address' => $ip, 'user_id' => $user->id]);

        if ($throttle) {
            $throttle->resetAttempts();
        }
    }

    public function unlock(string $ip): bool
    {
        $throttle = LoginThrottle::firstWhere('ip_address', $ip);

        if ($throttle) {
            $throttle->resetAttempts();
            return true;
        }

        return false;
    }

    public function getAttempts(string $ip): int
    {
        $throttle = LoginThrottle::firstWhere('ip_address', $ip);
        return $throttle?->failed_attempts ?? 0;
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
