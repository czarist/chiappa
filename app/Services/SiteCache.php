<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Support\Facades\Cache;

class SiteCache
{
    public const SETTINGS = 'site.settings';

    public const THEME = 'site.theme';

    public const TTL = 600;

    public function settings(): array
    {
        return Cache::remember(self::SETTINGS, self::TTL, fn () => Setting::query()->pluck('value', 'key')->all());
    }

    public function menu(): ?Menu
    {
        return Menu::with('items.page')->where('location', 'primary')->first();
    }

    public function theme(): string
    {
        return Cache::remember(self::THEME, self::TTL, fn () => Theme::where('active', true)->first()?->slug ?? 'default');
    }

    public function page(string $slug): ?Page
    {
        return Page::with(['sections', 'seo'])->where('slug', $slug)->first();
    }

    public function forgetPage(?string $slug = null, ?string $previousSlug = null): void
    {
        // Settings/theme remain cached; page content is always read fresh.
    }

    public function forgetSettings(): void
    {
        Cache::forget(self::SETTINGS);
        Cache::forget(self::THEME);
    }

    public function forgetMenu(): void
    {
        // Menu is not cached as an Eloquent model.
    }

    public function flush(): void
    {
        Cache::forget(self::SETTINGS);
        Cache::forget(self::THEME);
        try {
            Cache::flush();
        } catch (\Throwable) {
            // Ignore stores that cannot flush.
        }
    }
}
