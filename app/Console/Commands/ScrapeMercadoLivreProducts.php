<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Setting;
use App\Services\MediaService;
use App\Services\MercadoLivreService;
use App\Services\SiteCache;
use Illuminate\Console\Command;

class ScrapeMercadoLivreProducts extends Command
{
    protected $signature = 'mercadolivre:scrape';

    protected $description = 'Atualiza a vitrine de produtos da home com os anúncios ativos do Mercado Livre';

    public function handle(MercadoLivreService $service, MediaService $media, SiteCache $cache): int
    {
        if (! $service->isConnected()) {
            $this->error('Mercado Livre não está conectado. Acesse /admin/mercadolivre/connect logado como admin para autorizar.');

            return self::FAILURE;
        }

        $slug = Setting::where('key', 'home_page')->first()?->value[0] ?? 'inicio';
        $page = Page::with('sections')->where('slug', $slug)->first();
        $section = $page?->sections->firstWhere('type', 'products');

        if (! $section) {
            $this->error("Nenhuma seção do tipo 'products' encontrada na página '{$slug}'.");

            return self::FAILURE;
        }

        try {
            $products = $service->fetchSellerItems();
        } catch (\Throwable $e) {
            $this->error('Falha ao buscar produtos no Mercado Livre: '.$e->getMessage());

            return self::FAILURE;
        }

        if (empty($products)) {
            $this->warn('Nenhum produto ativo encontrado. Mantendo a vitrine atual.');

            return self::SUCCESS;
        }

        $content = $section->content ?? [];
        $content['items'] = $media->localizeContent($products);
        $section->update(['content' => $content]);

        $cache->forgetPage($slug);

        $this->info(count($products).' produtos atualizados na vitrine.');

        return self::SUCCESS;
    }
}
