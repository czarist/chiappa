@php
    $instagramUrl = $content['url'] ?? 'https://www.instagram.com/chiappa.autopecas/';
    $instagramTitle = e($content['title'] ?? 'Siga a gente no Instagram');
    $instagramTitle = str_replace('Instagram', '<span>Instagram</span>', $instagramTitle);
    $stats = $site['instagram_stats'] ?? null;

    $posts = collect($stats['posts'] ?? [])->map(fn ($post) => [
        'image' => str_replace('http://localhost:8080', '', $post['image'] ?? ''),
        'alt' => 'Post Instagram - '.($post['caption'] !== '' ? \Illuminate\Support\Str::limit($post['caption'], 40) : 'Chiappa Auto Peças'),
        'likes' => number_format($post['likes'] ?? 0, 0, ',', '.').' curtidas',
        'text' => $post['caption'] ?? '',
        'permalink' => $post['permalink'] ?? $instagramUrl,
    ])->all();

    if (empty($posts)) {
        $posts = [
            ['image' => asset('assets/instagram/post-1.jpg'), 'alt' => 'A loja está de cara nova', 'likes' => '13 curtidas', 'text' => 'A loja está de cara nova! Mas continua com a melhor qualidade, preços e ótimo atendimento de sempre.', 'permalink' => 'https://www.instagram.com/p/DIo5HD4R_Fq/'],
            ['image' => asset('assets/instagram/post-2.jpg'), 'alt' => 'Teste de rodagem após manutenção', 'likes' => '6 curtidas', 'text' => 'Na Chiappa Auto Peças e Serviços, cada manutenção é finalizada com teste de rodagem, porque segurança não é detalhe e sim prioridade.', 'permalink' => 'https://www.instagram.com/p/DYPJMpxu4hE/'],
            ['image' => asset('assets/instagram/post-3.jpg'), 'alt' => 'Peças genuínas e de primeira linha', 'likes' => '14 curtidas', 'text' => 'Na hora de cuidar do seu carro, não dá pra arriscar. Aqui na Chiappa Auto Peças, você encontra peças genuínas e de primeira linha.', 'permalink' => 'https://www.instagram.com/p/DYCemxEDuKk/'],
            ['image' => asset('assets/instagram/post-4.jpg'), 'alt' => 'Feliz Páscoa', 'likes' => '5 curtidas', 'text' => 'Desejamos a todos os nossos clientes e amigos uma Feliz Páscoa! Que seja um momento de renovação, paz e muitos caminhos seguros pela frente.', 'permalink' => 'https://www.instagram.com/p/DWwH4sXjoWg/'],
            ['image' => asset('assets/instagram/post-5.jpg'), 'alt' => 'Acabamento e proteção do veículo', 'likes' => '4 curtidas', 'text' => 'Seu carro merece atenção nos detalhes. Brilho intenso, proteção duradoura e acabamento profissional.', 'permalink' => 'https://www.instagram.com/p/DVBLrVujrAC/'],
            ['image' => asset('assets/instagram/post-6.jpg'), 'alt' => 'Qualidade e preço justo da Chiappa', 'likes' => '4 curtidas', 'text' => 'O Brad Pitt não é real... Mas a qualidade e o preço justo da Chiappa Autopeças são.', 'permalink' => 'https://www.instagram.com/p/DTVG75zjghd/'],
        ];
    }

    $postsCount = $stats['posts_count'] ?? 46;
    $followersCount = $stats['followers'] ?? 937;
    $followingCount = $stats['following'] ?? 1809;
    $profilePic = $stats['profile_pic'] ?? asset('assets/logo-social.jpg');
@endphp
<section class="section section-surface"><div class="shell instagram-block"><div class="section-heading instagram-heading" data-aos="fade-up"><h2>{!! $instagramTitle !!}</h2><p>{{ $content['subtitle'] ?? 'Acompanhe nosso dia a dia no Instagram' }}</p></div><div class="instagram-profile" data-aos="fade-up"><img src="{{ $profilePic }}" alt="Chiappa Auto Peças e Serviços"><div class="instagram-profile-copy"><strong>@chiappa.autopecas</strong><small>⚙Auto pecas e Oficina mecânica</small><div class="instagram-metrics"><span><b>{{ number_format($postsCount, 0, ',', '.') }}</b> publicações</span><span><b>{{ number_format($followersCount, 0, ',', '.') }}</b> seguidores</span><span><b>{{ number_format($followingCount, 0, ',', '.') }}</b> seguindo</span></div></div><a class="button button-small" href="{{ $instagramUrl }}" target="_blank" rel="noopener">Seguir</a></div><div class="instagram-grid">@foreach($posts as $post)<article class="instagram-post" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 75 }}"><div class="instagram-post-head"><img src="{{ $profilePic }}" alt="Chiappa Auto Peças e Serviços"><div><strong>chiappa.autopecas</strong><small>Canoas, RS</small></div><i class="fab fa-instagram"></i></div><a class="instagram-image-wrap" href="{{ $post['permalink'] }}" target="_blank" rel="noopener"><img src="{{ $post['image'] }}" alt="{{ $post['alt'] }}" loading="lazy"></a><div class="instagram-post-body"><div class="instagram-actions"><i class="fas fa-heart"></i><i class="far fa-comment"></i><i class="far fa-paper-plane"></i></div><small>{{ $post['likes'] }}</small><p><strong>chiappa.autopecas</strong> {{ $post['text'] }}</p></div></article>@endforeach</div><div class="instagram-follow" data-aos="fade-up"><a class="button" href="{{ $instagramUrl }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Ver mais no Instagram</a></div></div></section>
