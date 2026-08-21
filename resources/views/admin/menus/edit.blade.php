@extends('layouts.admin')
@section('title', 'Menu')
@section('content')
<div class="page-heading">
    <div>
        <h1>Menu principal</h1>
        <p class="text-muted">Links do cabeçalho: páginas, âncoras ou URLs externas.</p>
    </div>
</div>
<form method="post" action="{{ route('admin.menu.update') }}" id="menu-form">
    @csrf @method('PUT')
    <div class="admin-card">
        <div class="field"><label>Nome</label><input name="name" value="{{ $menu->name }}" required></div>
        <h3>Itens</h3>
        <div id="menu-items">
            @forelse($menu->items as $i => $item)
                @include('admin.menus.item', ['i' => $i, 'item' => $item, 'pages' => $pages])
            @empty
                @include('admin.menus.item', ['i' => 0, 'item' => null, 'pages' => $pages])
            @endforelse
        </div>
        <div class="form-actions" style="justify-content:space-between">
            <button type="button" class="admin-button secondary" id="add-menu-item"><i class="fas fa-plus"></i> Adicionar item</button>
            <button class="admin-button" type="submit">Salvar menu</button>
        </div>
    </div>
</form>
<template id="menu-item-template">
    @include('admin.menus.item', ['i' => '__INDEX__', 'item' => null, 'pages' => $pages])
</template>
<script>
(function(){
    var list = document.getElementById('menu-items');
    var tpl = document.getElementById('menu-item-template');
    document.getElementById('add-menu-item').addEventListener('click', function(){
        var index = list.querySelectorAll('.menu-row').length;
        list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__INDEX__/g, index));
    });
    list.addEventListener('click', function(e){
        var btn = e.target.closest('[data-remove-item]');
        if (!btn) return;
        var row = btn.closest('.menu-row');
        if (list.querySelectorAll('.menu-row').length === 1) {
            row.querySelectorAll('input[type="text"],select').forEach(function(el){ el.value = ''; });
            return;
        }
        row.remove();
    });
})();
</script>
@endsection
