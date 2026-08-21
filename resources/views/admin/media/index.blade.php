@extends('layouts.admin')
@section('title', 'Mídia')
@section('content')
<div class="page-heading">
    <div>
        <h1>Mídia</h1>
        <p class="text-muted">Imagens usadas nas páginas. Também dá para enviar direto no editor.</p>
    </div>
</div>
<div class="admin-card">
    <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="form-actions" style="justify-content:flex-start">
        @csrf
        <input type="file" name="file" accept="image/*" required>
        <input name="alt_text" placeholder="Texto alternativo">
        <button class="admin-button" type="submit"><i class="fas fa-upload"></i> Enviar</button>
    </form>
</div>
<div class="media-grid">
    @forelse($media as $item)
        <article class="media-card">
            <img src="{{ $item->url() }}" alt="{{ $item->alt_text ?? $item->name }}">
            <div>
                <strong title="{{ $item->name }}">{{ $item->name }}</strong>
                <p class="text-muted">{{ number_format($item->size / 1024, 1, ',', '.') }} KB</p>
                <div class="actions">
                    <button class="admin-button secondary small" type="button" data-copy="{{ $item->url() }}">Copiar URL</button>
                    <form method="post" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Excluir esta mídia?')">
                        @csrf @method('DELETE')
                        <button class="admin-button danger small" type="submit">Excluir</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="empty">Nenhuma mídia enviada.</div>
    @endforelse
</div>
{{ $media->links() }}
<script>
document.querySelectorAll('[data-copy]').forEach(function(btn){
    btn.addEventListener('click', function(){
        navigator.clipboard.writeText(btn.dataset.copy).then(function(){ btn.textContent = 'Copiado'; setTimeout(function(){ btn.textContent = 'Copiar URL'; }, 1200); });
    });
});
</script>
@endsection
