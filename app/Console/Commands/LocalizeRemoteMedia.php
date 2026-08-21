<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Setting;
use App\Services\MediaService;
use App\Services\SiteCache;
use Illuminate\Console\Command;

class LocalizeRemoteMedia extends Command
{
    protected $signature = 'media:localize';

    protected $description = 'Baixa imagens remotas das páginas e configurações para public/assets/uploads';

    public function handle(MediaService $media, SiteCache $cache): int
    {
        $count = 0;

        foreach (Page::with('sections')->get() as $page) {
            foreach ($page->sections as $section) {
                $localized = $media->localizeContent($section->content ?? []);
                if ($localized !== $section->content) {
                    $section->update(['content' => $localized]);
                    $count++;
                }
            }
            $cache->forgetPage($page->slug);
        }

        foreach (Setting::all() as $setting) {
            $localized = $media->localizeContent($setting->value ?? []);
            if ($localized !== $setting->value) {
                $setting->update(['value' => $localized]);
                $count++;
            }
        }

        $cache->forgetSettings();
        $this->info($count === 0 ? 'Nenhuma imagem remota encontrada.' : $count.' blocos atualizados com imagens locais.');

        return self::SUCCESS;
    }
}
