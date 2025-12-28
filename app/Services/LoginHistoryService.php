<?php

namespace App\Services;

use App\Models\LoginHistory;
use Illuminate\Support\Collection;

class LoginHistoryService
{
    public function recordLogin(int $userId, string $ip, ?string $userAgent = null): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'action' => 'login',
        ]);
    }

    public function recordLogout(int $userId, string $ip, ?string $userAgent = null): LoginHistory
    {
        return LoginHistory::create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'action' => 'logout',
        ]);
    }

    public function getLoginHistory(int $userId, int $limit = 20): Collection
    {
        return LoginHistory::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
