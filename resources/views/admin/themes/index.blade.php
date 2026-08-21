@extends('layouts.admin')
@section('title', 'Temas')
@section('content')
<div class="page-heading">
    <div>
        <h1>Temas</h1>
        <p class="text-muted">As três variações usam os mesmos componentes, só muda a paleta.</p>
    </div>
</div>
<div class="theme-grid">
    @foreach($themes as $theme)
        <div class="theme-card {{ $theme->active ? 'active' : '' }}">
            <div class="theme-preview {{ $theme->slug }}"></div>
            <h2>{{ $theme->name }}</h2>
            <p class="text-muted">{{ $theme->active ? 'Tema ativo' : 'Disponível' }}</p>
            @if(! $theme->active)
                <form method="post" action="{{ route('admin.themes.activate', $theme) }}">@csrf<button class="admin-button" type="submit">Ativar tema</button></form>
            @endif
        </div>
    @endforeach
</div>
@endsection
