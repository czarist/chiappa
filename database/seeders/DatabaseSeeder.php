<?php

namespace Database\Seeders;

use App\Models\{Menu, Page, Setting, Theme, User};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => env('ADMIN_EMAIL', 'admin@example.com')], ['name' => env('ADMIN_NAME', 'Administrador'), 'password' => env('ADMIN_PASSWORD', 'change-me'), 'role' => 'administrator', 'status' => 'active']);
        foreach ([['name' => 'Padrão', 'slug' => 'default', 'settings' => ['body' => 'dark']], ['name' => 'Cinza', 'slug' => 'gray', 'settings' => ['body' => 'gray']], ['name' => 'Claro', 'slug' => 'light', 'settings' => ['body' => 'light']]] as $i => $theme) Theme::updateOrCreate(['slug' => $theme['slug']], $theme + ['active' => $i === 0]);
        $page = Page::updateOrCreate(['slug' => 'inicio'], ['title' => 'Chiappa Auto Peças e Serviços', 'status' => 'published', 'template' => 'default', 'published_at' => now()]);
        $page->seo()->updateOrCreate([], ['title' => 'Chiappa Auto Peças e Serviços | Canoas/RS', 'description' => 'Auto peças de qualidade e oficina especializada em Canoas/RS.', 'indexable' => true, 'followable' => true, 'og_title' => 'Chiappa Auto Peças e Serviços']);
        if ($page->sections()->count() === 0) foreach ($this->sections() as $position => $section) $page->sections()->create(['type' => $section['type'], 'position' => $position, 'content' => $section['content']]);
        Setting::updateOrCreate(['key' => 'site'], ['value' => ['name' => 'Chiappa Auto Peças e Serviços', 'description' => 'Auto peças de qualidade e oficina especializada em Canoas/RS.', 'logo' => '/assets/logo.png', 'favicon' => '/assets/favicon.svg', 'phone' => '(51) 98453-1794', 'whatsapp' => '5551984531794', 'email' => '', 'address' => 'Rua Cristóvão Colombo, 33 - Niterói, Canoas/RS - CEP 92.110-450', 'instagram' => 'https://www.instagram.com/chiappa.autopecas/', 'copyright' => '© 2026 Chiappa Auto Peças e Serviços. Todos os direitos reservados.', 'url' => env('APP_URL', 'http://localhost')]]);
        Setting::updateOrCreate(['key' => 'home_page'], ['value' => ['inicio']]);
        $menu = Menu::firstOrCreate(['location' => 'primary'], ['name' => 'Menu principal']);
        if ($menu->items()->count() === 0) foreach ([['label' => 'Início', 'url' => '#inicio'], ['label' => 'Serviços', 'url' => '#servicos'], ['label' => 'Contato', 'url' => '#contato']] as $position => $item) $menu->items()->create($item + ['position' => $position, 'target' => '_self']);
    }

    private function sections(): array
    {
        return [
            ['type' => 'hero', 'content' => ['title' => 'Performance e Confiança para o seu veículo', 'text' => 'Auto peças de qualidade e oficina especializada em Canoas/RS.', 'button' => 'Fale no WhatsApp', 'button_url' => 'https://wa.me/5551984531794', 'image' => 'https://images.pexels.com/photos/4489735/pexels-photo-4489735.jpeg?auto=compress&cs=tinysrgb&w=1920']],
            ['type' => 'differentials', 'content' => ['title' => 'Nossos Diferenciais', 'subtitle' => 'Qualidade, precisão e confiança', 'items' => [['icon' => 'fa-shield-halved', 'title' => 'Peças de Qualidade', 'text' => 'Fornecedores confiáveis e peças com garantia para o seu veículo.'], ['icon' => 'fa-user-gear', 'title' => 'Equipe Especializada', 'text' => 'Mecânicos experientes e atualizados com as principais marcas.'], ['icon' => 'fa-bolt', 'title' => 'Atendimento Rápido', 'text' => 'Diagnóstico ágil e orçamento sem enrolação, direto no WhatsApp.'], ['icon' => 'fa-tag', 'title' => 'Melhor Preço', 'text' => 'Preço justo e transparente, sem surpresas na hora de pagar.']]]],
            ['type' => 'services', 'content' => ['title' => 'Nossos Serviços', 'subtitle' => 'Tudo que seu veículo precisa em um só lugar', 'items' => [['icon' => 'fa-boxes-stacked', 'title' => 'Venda de Auto Peças', 'text' => 'Peças originais e paralelas de qualidade para as principais marcas e modelos.'], ['icon' => 'fa-gears', 'title' => 'Mecânica Geral', 'text' => 'Motor, câmbio, embreagem e correias com equipe experiente e diagnóstico preciso.'], ['icon' => 'fa-car-burst', 'title' => 'Sistema de Freios', 'text' => 'Pastilhas, discos, fluido e revisão completa para sua segurança na estrada.'], ['icon' => 'fa-car-side', 'title' => 'Suspensão e Direção', 'text' => 'Amortecedores, molas e alinhamento para mais conforto e estabilidade.'], ['icon' => 'fa-microchip', 'title' => 'Diagnóstico Eletrônico', 'text' => 'Scanner automotivo para identificar falhas com rapidez e precisão.'], ['icon' => 'fa-oil-can', 'title' => 'Manutenção Preventiva', 'text' => 'Troca de óleo, filtros e revisões periódicas para evitar problemas maiores.']]]],
            ['type' => 'products', 'content' => ['title' => 'Nossa Vitrine no Mercado Livre', 'subtitle' => 'Confira alguns produtos disponíveis', 'items' => [['title' => 'Pastilha de Freio Dianteira', 'price' => 'R$ 149,90', 'image' => 'https://images.pexels.com/photos/13350018/pexels-photo-13350018.jpeg?auto=compress&cs=tinysrgb&w=800'], ['title' => 'Filtro de Óleo', 'price' => 'R$ 39,90', 'image' => 'https://cdn.pixabay.com/photo/2015/10/18/21/41/oil-filter-995249_640.jpg'], ['title' => 'Amortecedor Traseiro', 'price' => 'R$ 289,90', 'image' => 'https://images.pexels.com/photos/5542145/pexels-photo-5542145.jpeg?auto=compress&cs=tinysrgb&w=800'], ['title' => 'Disco de Freio Ventilado', 'price' => 'R$ 219,90', 'image' => 'https://images.pexels.com/photos/3642618/pexels-photo-3642618.jpeg?auto=compress&cs=tinysrgb&w=800'], ['title' => 'Kit Correia Dentada', 'price' => 'R$ 349,90', 'image' => 'https://images.pexels.com/photos/5121549/pexels-photo-5121549.jpeg?auto=compress&cs=tinysrgb&w=800'], ['title' => 'Jogo de Velas de Ignição', 'price' => 'R$ 89,90', 'image' => 'https://images.pexels.com/photos/36086528/pexels-photo-36086528.jpeg?auto=compress&cs=tinysrgb&w=800']]]],
            ['type' => 'instagram', 'content' => ['title' => 'Siga a gente no Instagram', 'subtitle' => 'Conteúdo, novidades e dicas para cuidar do seu veículo.', 'url' => 'https://www.instagram.com/chiappa.autopecas/']],
            ['type' => 'stats', 'content' => ['items' => [['value' => '+15', 'label' => 'Anos de experiência'], ['value' => '+5000', 'label' => 'Clientes atendidos'], ['value' => '+2000', 'label' => 'Peças em estoque'], ['value' => '4.9', 'label' => 'Avaliação média']]]],
            ['type' => 'cta', 'content' => ['title' => 'Vem tomar um café com a gente', 'text' => 'Fale agora com nossa equipe pelo WhatsApp.', 'button' => '(51) 98453-1794', 'button_url' => 'https://wa.me/5551984531794']],
            ['type' => 'contact', 'content' => ['title' => 'Fale Conosco', 'text' => 'Estamos prontos para te atender. Fale pelo WhatsApp ou venha até a nossa loja.', 'phone' => '(51) 98453-1794', 'address' => 'Rua Cristóvão Colombo, 33<br>Niterói, Canoas/RS<br>CEP 92.110-450', 'hours' => 'Segunda a Sexta<br>08h–12h e 13h30–18h<br><br>Sábado<br>08h–12h', 'map' => 'https://www.google.com/maps?q=Rua+Crist%C3%B3v%C3%A3o+Colombo,+33+-+Niter%C3%B3i,+Canoas+-+RS,+92110-450&output=embed']],
        ];
    }
}
