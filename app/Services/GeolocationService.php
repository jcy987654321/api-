<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeolocationService
{
    private string $apiUrl = 'https://ipapi.co';
    private int $cacheDuration = 86400; // 24 hours

    public function getLocationByIp(string $ip): array
    {
        $cacheKey = "geolocation:{$ip}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($ip) {
            try {
                if ($this->isPrivateIp($ip)) {
                    return $this->getDefaultLocation();
                }

                $response = Http::timeout(5)->get("{$this->apiUrl}/{$ip}/json/");

                if (!$response->successful()) {
                    return $this->getDefaultLocation();
                }

                $data = $response->json();

                return [
                    'country' => $data['country_code'] ?? null,
                    'country_name' => $data['country_name'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => (float)($data['latitude'] ?? 0),
                    'longitude' => (float)($data['longitude'] ?? 0),
                ];
            } catch (\Exception $e) {
                return $this->getDefaultLocation();
            }
        });
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    private function getDefaultLocation(): array
    {
        return [
            'country' => null,
            'country_name' => 'Unknown',
            'city' => 'Unknown',
            'latitude' => 0,
            'longitude' => 0,
        ];
    }
}
