<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Administração') | Chiappa CMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-body">
<aside class="admin-sidebar">
    <a class="admin-logo" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('assets/logo.png') }}" alt="Chiappa">
        <span>CMS</span>
    </a>
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a>
        <span>Conteúdo</span>
        <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'is-active' : '' }}"><i class="fas fa-file-lines"></i> Páginas</a>
        <a href="{{ route('admin.menu.edit') }}" class="{{ request()->routeIs('admin.menu.*') ? 'is-active' : '' }}"><i class="fas fa-bars"></i> Menu</a>
        <a href="{{ route('admin.media.index') }}" class="{{ request()->routeIs('admin.media.*') ? 'is-active' : '' }}"><i class="fas fa-images"></i> Mídia</a>
        <span>Configuração</span>
        <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}"><i class="fas fa-sliders"></i> Site</a>
        <a href="{{ route('admin.themes.index') }}" class="{{ request()->routeIs('admin.themes.*') ? 'is-active' : '' }}"><i class="fas fa-palette"></i> Tema</a>
        @if(auth()->user()->isAdministrator())
            <span>Administração</span>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"><i class="fas fa-users"></i> Usuários</a>
        @endif
    </nav>
    <div class="admin-sidebar-foot">
        <form method="post" action="{{ route('admin.cache.clear') }}">
            @csrf
            <button type="submit" title="Limpar cache do site"><i class="fas fa-broom"></i> Limpar cache</button>
        </form>
    </div>
</aside>
<div class="admin-main">
    <header class="admin-top">
        <strong>@yield('title', 'Dashboard')</strong>
        <div class="admin-top-actions">
            <a class="admin-ghost" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> Ver site</a>
            <div class="admin-user">
                <span class="admin-avatar">{{ auth()->user()->initials() }}</span>
                <div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->roleLabel() }}</small>
                </div>
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="admin-ghost" type="submit" title="Sair"><i class="fas fa-right-from-bracket"></i></button>
            </form>
        </div>
    </header>
    <main class="admin-content">
        @if (session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash error">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
