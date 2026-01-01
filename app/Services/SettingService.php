<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService extends BaseService
{
    public const CACHE_KEY_ALL = 'cms:settings:all';

    public const DEFAULTS = [
        'basic' => [
            [
                'key' => 'site_name',
                'value' => 'API Project',
                'type' => 'string',
                'description' => '网站名称',
            ],
            [
                'key' => 'site_description',
                'value' => 'API 项目网站描述',
                'type' => 'text',
                'description' => '网站描述',
            ],
            [
                'key' => 'site_keywords',
                'value' => 'API, Laravel',
                'type' => 'string',
                'description' => '网站关键词',
            ],
            [
                'key' => 'site_url',
                'value' => '',
                'type' => 'string',
                'description' => '网站URL',
            ],
            [
                'key' => 'site_email',
                'value' => '',
                'type' => 'string',
                'description' => '网站邮箱',
            ],
            [
                'key' => 'site_phone',
                'value' => '',
                'type' => 'string',
                'description' => '网站电话',
            ],
        ],
        'seo' => [
            [
                'key' => 'seo_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => '是否启用SEO',
            ],
            [
                'key' => 'seo_title_prefix',
                'value' => '',
                'type' => 'string',
                'description' => '标题前缀',
            ],
            [
                'key' => 'seo_title_suffix',
                'value' => '',
                'type' => 'string',
                'description' => '标题后缀',
            ],
            [
                'key' => 'google_analytics',
                'value' => '',
                'type' => 'string',
                'description' => 'Google Analytics ID',
            ],
            [
                'key' => 'baidu_analytics',
                'value' => '',
                'type' => 'string',
                'description' => '百度统计ID',
            ],
        ],
        'social' => [
            [
                'key' => 'social_wechat',
                'value' => '',
                'type' => 'string',
                'description' => '微信公众号',
            ],
            [
                'key' => 'social_qq',
                'value' => '',
                'type' => 'string',
                'description' => 'QQ',
            ],
            [
                'key' => 'social_weibo',
                'value' => '',
                'type' => 'string',
                'description' => '微博',
            ],
            [
                'key' => 'social_github',
                'value' => '',
                'type' => 'string',
                'description' => 'GitHub',
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'string',
                'description' => 'Twitter',
            ],
        ],
        'mail' => [
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
                'type' => 'string',
                'description' => '邮件驱动',
            ],
            [
                'key' => 'mail_from_address',
                'value' => '',
                'type' => 'string',
                'description' => '发送地址',
            ],
            [
                'key' => 'mail_from_name',
                'value' => '',
                'type' => 'string',
                'description' => '发送名称',
            ],
            [
                'key' => 'smtp_host',
                'value' => '',
                'type' => 'string',
                'description' => 'SMTP主机',
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'string',
                'description' => 'SMTP端口',
            ],
        ],
        'beian' => [
            [
                'key' => 'beian_number',
                'value' => '',
                'type' => 'string',
                'description' => '备案号',
            ],
            [
                'key' => 'beian_url',
                'value' => '',
                'type' => 'string',
                'description' => '备案链接',
            ],
            [
                'key' => 'company_name',
                'value' => '',
                'type' => 'string',
                'description' => '公司名称',
            ],
        ],
    ];

    public function ensureDefaults(): void
    {
        foreach (self::DEFAULTS as $group => $items) {
            foreach ($items as $item) {
                Setting::query()->firstOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => (string) $item['value'],
                        'type' => (string) $item['type'],
                        'group' => (string) $group,
                        'description' => $item['description'] ?? null,
                    ]
                );
            }
        }

        $this->clearCache();
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->all();

        $row = $settings[$key] ?? null;
        if (!is_array($row)) {
            return $default;
        }

        return $this->castValue($row['value'] ?? null, $row['type'] ?? 'string', $default);
    }

    public function set(string $key, $value, ?string $type = null, ?string $group = null, ?string $description = null): Setting
    {
        $setting = Setting::query()->where('key', $key)->first();

        if (!$setting) {
            $setting = new Setting();
            $setting->key = $key;
        }

        if ($type !== null) {
            $setting->type = $type;
        }

        if ($group !== null) {
            $setting->group = $group;
        }

        if ($description !== null) {
            $setting->description = $description;
        }

        $setting->value = $this->normalizeValueForStorage($value, $setting->type ?? 'string');
        $setting->save();

        $this->clearCache();

        return $setting;
    }

    public function getGroup(string $group): array
    {
        $settings = $this->all();

        $grouped = [];
        foreach ($settings as $key => $row) {
            if (!is_array($row)) {
                continue;
            }

            if (($row['group'] ?? null) !== $group) {
                continue;
            }

            $grouped[$key] = $this->castValue($row['value'] ?? null, $row['type'] ?? 'string');
        }

        return $grouped;
    }

    public function setGroup(string $group, array $values): void
    {
        $existing = Setting::query()->where('group', $group)->get()->keyBy('key');

        foreach ($values as $key => $value) {
            $setting = $existing->get($key);
            if (!$setting) {
                continue;
            }

            $setting->value = $this->normalizeValueForStorage($value, $setting->type ?? 'string');
            $setting->save();
        }

        $this->clearCache();
    }

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY_ALL, function () {
            return Setting::query()
                ->orderBy('group')
                ->orderBy('key')
                ->get()
                ->mapWithKeys(function (Setting $setting) {
                    return [
                        $setting->key => [
                            'key' => $setting->key,
                            'value' => $setting->value,
                            'type' => $setting->type,
                            'group' => $setting->group,
                            'description' => $setting->description,
                        ],
                    ];
                })
                ->all();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
    }

    private function castValue($value, string $type, $default = null)
    {
        if ($value === null) {
            return $default;
        }

        $type = strtolower($type);

        if ($type === 'boolean') {
            if (is_bool($value)) {
                return $value;
            }

            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($type === 'json') {
            if (is_array($value)) {
                return $value;
            }

            $decoded = json_decode((string) $value, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
        }

        return (string) $value;
    }

    private function normalizeValueForStorage($value, string $type): string
    {
        $type = strtolower($type);

        if ($type !== 'boolean' && $value === null) {
            return '';
        }

        if ($type === 'boolean') {
            return $value ? '1' : '0';
        }

        if ($type === 'json') {
            if (is_string($value)) {
                return $value;
            }

            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }
}
