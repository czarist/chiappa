@php($config = $site['site'] ?? [])
@php($theme = app(\App\Services\SiteCache::class)->theme())
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<x-site.seo :page="$page" :site="$site" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-{{ $theme }}">
<x-site.header :config="$config" />
<main>@yield('content')</main>
<x-site.footer :config="$config" />
<a class="whatsapp-float" href="https://wa.me/{{ $config['whatsapp'] ?? '5551984531794' }}" target="_blank" rel="noopener" aria-label="Fale no WhatsApp"><i class="fab fa-whatsapp"></i></a>
</body>
</html>

