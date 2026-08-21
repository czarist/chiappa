@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p class="text-muted">Resumo do conteúdo e atalhos do CMS.</p>
    </div>
    <a class="admin-button" href="{{ route('admin.pages.create') }}"><i class="fas fa-plus"></i> Nova página</a>
</div>
<div class="stats-grid">
    @foreach(['pages' => 'Páginas', 'published' => 'Publicadas', 'drafts' => 'Rascunhos', 'users' => 'Usuários', 'media' => 'Mídias'] as $key => $label)
        <div class="stat-box"><strong>{{ $stats[$key] }}</strong><span>{{ $label }}</span></div>
    @endforeach
</div>
<div class="quick-links">
    <a class="quick-link" href="{{ route('admin.pages.index') }}"><i class="fas fa-file-lines"></i><strong>Páginas</strong><span>Editar a home e seções</span></a>
    <a class="quick-link" href="{{ route('admin.media.index') }}"><i class="fas fa-images"></i><strong>Mídia</strong><span>Enviar imagens do site</span></a>
    <a class="quick-link" href="{{ route('admin.settings.edit') }}"><i class="fas fa-sliders"></i><strong>Configurações</strong><span>Telefone, endereço e logo</span></a>
    <a class="quick-link" href="{{ route('admin.menu.edit') }}"><i class="fas fa-bars"></i><strong>Menu</strong><span>Links do cabeçalho</span></a>
</div>
<div class="admin-card">
    <h2>Últimas alterações</h2>
    <table class="admin-table">
        <tr><th>Página</th><th>Status</th><th>Atualizada</th><th></th></tr>
        @forelse($latest as $page)
            <tr>
                <td>{{ $page->title }}</td>
                <td><span class="badge {{ $page->status }}">{{ ['published' => 'Publicado', 'draft' => 'Rascunho', 'scheduled' => 'Agendado'][$page->status] ?? $page->status }}</span></td>
                <td>{{ $page->updated_at->format('d/m/Y H:i') }}</td>
                <td><a class="icon-action" href="{{ route('admin.pages.edit', $page) }}" title="Editar"><i class="fas fa-pen"></i></a></td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Nenhuma página encontrada.</td></tr>
        @endforelse
    </table>
</div>
@endsection
