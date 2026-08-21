<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function all(): array
    {
        return Cache::rememberForever('site.settings', fn () => Setting::query()->pluck('value', 'key')->all());
    }

    public function put(array $values): void
    {
        foreach ($values as $key => $value) Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? $value : [$value]]);
        Cache::forget('site.settings');
    }
}
