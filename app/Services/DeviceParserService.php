<?php

namespace App\Services;

class DeviceParserService
{
    public function parseUserAgent(string $userAgent): array
    {
        return [
            'device_type' => $this->getDeviceType($userAgent),
            'browser_name' => $this->getBrowserName($userAgent),
            'browser_version' => $this->getBrowserVersion($userAgent),
            'os_name' => $this->getOsName($userAgent),
            'os_version' => $this->getOsVersion($userAgent),
            'is_mobile' => $this->isMobile($userAgent),
        ];
    }

    private function isMobile(string $userAgent): bool
    {
        $mobilePatterns = [
            'Mobile',
            'Android',
            'iPhone',
            'iPad',
            'Windows Phone',
            'BlackBerry',
            'Opera Mini',
            'IEMobile',
        ];

        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getDeviceType(string $userAgent): string
    {
        if (stripos($userAgent, 'Mobile') !== false) {
            return 'mobile';
        }
        if (stripos($userAgent, 'Tablet') !== false || stripos($userAgent, 'iPad') !== false) {
            return 'tablet';
        }
        if (stripos($userAgent, 'Bot') !== false || stripos($userAgent, 'Crawler') !== false) {
            return 'bot';
        }

        return 'desktop';
    }

    private function getBrowserName(string $userAgent): ?string
    {
        if (preg_match('/MSIE\s(\d+)/i', $userAgent, $matches) || preg_match('/Trident.*rv:(\d+)/i', $userAgent, $matches)) {
            return 'Internet Explorer';
        }
        if (preg_match('/Firefox\s(\d+)/i', $userAgent, $matches)) {
            return 'Firefox';
        }
        if (preg_match('/Chrome\s(\d+)/i', $userAgent, $matches)) {
            return 'Chrome';
        }
        if (preg_match('/Safari\s(\d+)/i', $userAgent, $matches)) {
            return 'Safari';
        }
        if (preg_match('/Edge\s(\d+)/i', $userAgent, $matches) || preg_match('/Edg\s(\d+)/i', $userAgent, $matches)) {
            return 'Edge';
        }
        if (preg_match('/Opera\s(\d+)/i', $userAgent, $matches)) {
            return 'Opera';
        }

        return null;
    }

    private function getBrowserVersion(string $userAgent): ?string
    {
        if (preg_match('/MSIE\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/Firefox\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/Chrome\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/Safari\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/Edg\s(\d+\.\d+)/i', $userAgent, $matches) || preg_match('/Edge\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/Opera\s(\d+\.\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getOsName(string $userAgent): ?string
    {
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        }
        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            return 'macOS';
        }
        if (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        }
        if (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }
        if (preg_match('/iPhone|iPad/i', $userAgent)) {
            return 'iOS';
        }

        return null;
    }

    private function getOsVersion(string $userAgent): ?string
    {
        if (preg_match('/Windows NT\s([\d.]+)/i', $userAgent, $matches)) {
            return $this->normalizeWindowsVersion($matches[1]);
        }
        if (preg_match('/Mac OS X\s([\d_]+)/i', $userAgent, $matches)) {
            return str_replace('_', '.', $matches[1]);
        }
        if (preg_match('/Android\s([\d.]+)/i', $userAgent, $matches)) {
            return $matches[1];
        }
        if (preg_match('/OS\s([\d_]+)/i', $userAgent, $matches)) {
            return str_replace('_', '.', $matches[1]);
        }

        return null;
    }

    private function normalizeWindowsVersion(string $version): string
    {
        $versionMap = [
            '10.0' => '10',
            '6.3' => '8.1',
            '6.2' => '8',
            '6.1' => '7',
            '6.0' => 'Vista',
        ];

        return $versionMap[$version] ?? $version;
    }
}
