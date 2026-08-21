<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Entrar | Chiappa CMS</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css'])
</head>
<body class="login-page">
<form class="login-box" method="post" action="{{ route('login.store', absolute: false) }}">
    @csrf
    <img src="{{ asset('assets/logo.png') }}" alt="Chiappa">
    <div>
        <h1>Entrar no CMS</h1>
        <p class="text-muted" style="text-align:center;margin:0">Acesso da equipe Chiappa Auto Peças</p>
    </div>
    <label>E-mail
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </label>
    <label>Senha
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    <label class="check">
        <input type="checkbox" name="remember" value="1"> Lembrar acesso
    </label>
    <button class="admin-button" type="submit">Entrar</button>
    @if (session('status'))
        <p class="error-text">{{ session('status') }}</p>
    @endif
    @error('email')
        <p class="error-text">{{ $message }}</p>
    @enderror
</form>
</body>
</html>
