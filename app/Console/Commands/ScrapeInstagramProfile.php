<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\InstagramScraperService;
use App\Services\MediaService;
use App\Services\SiteCache;
use Illuminate\Console\Command;

class ScrapeInstagramProfile extends Command
{
    protected $signature = 'instagram:scrape {username=chiappa.autopecas}';

    protected $description = 'Busca seguidores, publicações e posts recentes do Instagram e atualiza as configurações do site';

    public function handle(InstagramScraperService $scraper, MediaService $media, SiteCache $cache): int
    {
        $username = $this->argument('username');
        $data = $scraper->fetchProfile($username);

        if (! $data) {
            $this->error("Não foi possível obter os dados do Instagram para @{$username}. Mantendo o último valor salvo.");

            return self::FAILURE;
        }

        $data = $media->localizeContent($data);
        Setting::updateOrCreate(['key' => 'instagram_stats'], ['value' => $data]);
        $cache->forgetSettings();

        $this->info("Instagram atualizado: {$data['followers']} seguidores, {$data['posts_count']} publicações, ".count($data['posts']).' posts recentes.');

        return self::SUCCESS;
    }
}
