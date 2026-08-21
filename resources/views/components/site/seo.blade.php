@props(['page', 'site' => []])
@php
    $seo = $page->seo ?? new \App\Models\SeoMetadata();
    $meta = $seo->resolved($page, $site);
    $config = $site['site'] ?? [];
@endphp
<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
@if($meta['keywords'])<meta name="keywords" content="{{ $meta['keywords'] }}">@endif
@if($meta['author'])<meta name="author" content="{{ $meta['author'] }}">@endif
<meta name="robots" content="{{ $meta['robots'] }}">
<meta name="googlebot" content="{{ $meta['robots'] }}">
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta name="theme-color" content="{{ $meta['theme_color'] }}">
<meta name="format-detection" content="telephone=yes">
<meta name="geo.region" content="BR-RS">
<meta name="geo.placename" content="Canoas">
<meta name="ICBM" content="-29.918, -51.178">
<meta property="og:type" content="{{ $meta['og_type'] }}">
<meta property="og:locale" content="{{ $meta['og_locale'] }}">
<meta property="og:site_name" content="{{ $meta['og_site_name'] }}">
<meta property="og:url" content="{{ $meta['og_url'] }}">
<meta property="og:title" content="{{ $meta['og_title'] }}">
<meta property="og:description" content="{{ $meta['og_description'] }}">
@if($meta['og_image'])
<meta property="og:image" content="{{ $meta['og_image'] }}">
<meta property="og:image:alt" content="{{ $meta['og_image_alt'] }}">
@endif
<meta name="twitter:card" content="{{ $meta['twitter_card'] }}">
<meta name="twitter:title" content="{{ $meta['twitter_title'] }}">
<meta name="twitter:description" content="{{ $meta['twitter_description'] }}">
@if($meta['twitter_image'])<meta name="twitter:image" content="{{ $meta['twitter_image'] }}">@endif
@if($page->updated_at)<meta property="article:modified_time" content="{{ $page->updated_at->toAtomString() }}">@endif
<link rel="icon" href="{{ $meta['favicon'] }}">
<link rel="apple-touch-icon" href="{{ $config['logo'] ?? asset('assets/logo.png') }}">
<script type="application/ld+json">{!! json_encode($meta['json_ld'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
