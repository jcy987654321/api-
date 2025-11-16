<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginLogService
{
    public function __construct(
        private GeolocationService $geolocationService,
        private DeviceParserService $deviceParserService,
    ) {}

    public function recordLoginAttempt(
        Request $request,
        User $user,
        string $status = LoginLog::STATUS_SUCCESS,
        ?string $failureReason = null,
    ): LoginLog {
        $ip = $this->getClientIp($request);
        $userAgent = $request->userAgent() ?? '';

        $locationData = $this->geolocationService->getLocationByIp($ip);
        $deviceData = $this->deviceParserService->parseUserAgent($userAgent);

        $logData = [
            'user_id' => $user->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status' => $status,
            'failure_reason' => $failureReason,
            ...$locationData,
            ...$deviceData,
        ];

        return LoginLog::create($logData);
    }

    public function recordBlockedLogin(Request $request, string $reason = 'Throttle limit exceeded'): LoginLog
    {
        $ip = $this->getClientIp($request);
        $userAgent = $request->userAgent() ?? '';

        $locationData = $this->geolocationService->getLocationByIp($ip);
        $deviceData = $this->deviceParserService->parseUserAgent($userAgent);

        $logData = [
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status' => LoginLog::STATUS_BLOCKED,
            'failure_reason' => $reason,
            ...$locationData,
            ...$deviceData,
        ];

        return LoginLog::create($logData);
    }

    public function getLoginHistory(User $user, int $limit = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $user->loginLogs()
            ->orderByDesc('created_at')
            ->paginate($limit);
    }

    public function getSuspiciousActivities(User $user, int $days = 7)
    {
        return $user->loginLogs()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('status', '!=', LoginLog::STATUS_SUCCESS)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getNewDeviceLogins(User $user, int $limit = 5)
    {
        return $user->loginLogs()
            ->where('status', LoginLog::STATUS_SUCCESS)
            ->groupBy('device_type', 'browser_name', 'os_name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
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
