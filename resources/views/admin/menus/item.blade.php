<div class="menu-row">
    <div class="field"><label>Label</label><input name="items[{{ $i }}][label]" value="{{ $item->label ?? '' }}" required></div>
    <div class="field"><label>URL / âncora</label><input name="items[{{ $i }}][url]" value="{{ $item->url ?? '' }}"></div>
    <div class="field">
        <label>Página</label>
        <select name="items[{{ $i }}][page_id]">
            <option value="">Nenhuma</option>
            @foreach($pages as $page)
                <option value="{{ $page->id }}" @selected(($item->page_id ?? null) === $page->id)>{{ $page->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Abrir em</label>
        <select name="items[{{ $i }}][target]">
            <option value="_self" @selected(($item->target ?? '_self') === '_self')>Mesma aba</option>
            <option value="_blank" @selected(($item->target ?? '') === '_blank')>Nova aba</option>
        </select>
    </div>
    <label class="check"><input type="checkbox" name="items[{{ $i }}][active]" value="1" @checked($item->active ?? true)> Ativo</label>
    <button type="button" class="icon-action danger" data-remove-item title="Remover"><i class="fas fa-trash"></i></button>
</div>
