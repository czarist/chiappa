<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function __construct(private readonly SiteCache $cache) {}

    public function all(): array
    {
        return $this->cache->settings();
    }

    public function put(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? $value : [$value]]);
        }
        $this->cache->forgetSettings();
    }
}
