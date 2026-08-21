@extends('layouts.admin') @section('title',$page->exists?'Editar página':'Nova página') @section('content')<form method="post" action="{{ $page->exists ? route('admin.pages.update',$page) : route('admin.pages.store') }}">@csrf @if($page->exists) @method('PUT') @endif<div class="page-heading"><div><h1>{{ $page->exists ? 'Editar página' : 'Nova página' }}</h1><p class="text-muted">Conteúdo estruturado por seções.</p></div><div class="form-actions"><a class="admin-button secondary" href="{{ route('admin.pages.index') }}">Cancelar</a><button class="admin-button" type="submit">Salvar página</button></div></div><div class="form-grid"><div class="form-card"><div class="field"><label for="title">Título</label><input id="title" name="title" value="{{ old('title',$page->title) }}" required></div><div class="field"><label for="slug">Slug</label><input id="slug" name="slug" value="{{ old('slug',$page->slug) }}" placeholder="deixe vazio para gerar"></div><div class="field"><label>Seções da página</label><div id="section-editor" class="section-editor" data-initial="{{ json_encode($page->exists ? $page->sections->map(fn($s)=>['type'=>$s->type,'active'=>$s->active,'content'=>$s->content,'settings'=>$s->settings])->values() : []) }}"></div><div class="section-editor-add"><select id="add-section-type">@foreach($sectionTypes as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select> <button type="button" class="admin-button secondary" id="add-section-btn"><i class="fas fa-plus"></i> Adicionar seção</button></div><input type="hidden" name="sections_json" id="sections_json_input"></div></div><div><div class="form-card"><div class="field"><label for="status">Status</label><select id="status" name="status"><option value="draft" @selected(old('status',$page->status)==='draft')>Rascunho</option><option value="published" @selected(old('status',$page->status)==='published')>Publicado</option><option value="scheduled" @selected(old('status',$page->status)==='scheduled')>Agendado</option></select></div><div class="field"><label for="template">Template</label><input id="template" name="template" value="{{ old('template',$page->template ?: 'default') }}"></div><div class="field"><label for="published_at">Data de publicação</label><input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at',$page->published_at?->format('Y-m-d\TH:i')) }}"></div></div><div class="form-card"><h3>SEO</h3><div class="field"><label>Título SEO</label><input name="seo[title]" value="{{ old('seo.title',$page->seo?->title) }}"></div><div class="field"><label>Descrição</label><textarea name="seo[description]" style="min-height:100px">{{ old('seo.description',$page->seo?->description) }}</textarea></div><div class="field"><label>Canonical URL</label><input name="seo[canonical_url]" value="{{ old('seo.canonical_url',$page->seo?->canonical_url) }}"></div><label class="check"><input type="hidden" name="seo[indexable]" value="0"><input type="checkbox" name="seo[indexable]" value="1" @checked(old('seo.indexable',$page->seo?->indexable ?? true))> Indexável</label><br><label class="check"><input type="hidden" name="seo[followable]" value="0"><input type="checkbox" name="seo[followable]" value="1" @checked(old('seo.followable',$page->seo?->followable ?? true))> Seguir links</label></div></div></div></form>
<script>
(function(){
  var root = document.getElementById('section-editor');
  if (!root) return;
  var hidden = document.getElementById('sections_json_input');
  var addSelect = document.getElementById('add-section-type');
  var addBtn = document.getElementById('add-section-btn');

  var LABELS = {!! json_encode($sectionTypes) !!};

  var SCHEMAS = {
    hero: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'text', label:'Texto', type:'textarea'},
      {key:'button', label:'Texto do botão', type:'text'},
      {key:'button_url', label:'Link do botão', type:'text'},
      {key:'image', label:'Imagem de fundo', type:'image'}
    ]},
    differentials: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'subtitle', label:'Subtítulo', type:'text'}
    ], repeat: { key:'items', label:'Diferencial', itemLabel: function(i){ return i.title || 'Novo item'; }, fields: [
      {key:'icon', label:'Ícone (Font Awesome, ex: fa-shield-halved)', type:'text'},
      {key:'title', label:'Título', type:'text'},
      {key:'text', label:'Texto', type:'textarea'}
    ]}},
    services: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'subtitle', label:'Subtítulo', type:'text'}
    ], repeat: { key:'items', label:'Serviço', itemLabel: function(i){ return i.title || 'Novo item'; }, fields: [
      {key:'icon', label:'Ícone (Font Awesome, ex: fa-gears)', type:'text'},
      {key:'title', label:'Título', type:'text'},
      {key:'text', label:'Texto', type:'textarea'}
    ]}},
    products: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'subtitle', label:'Subtítulo', type:'text'}
    ], repeat: { key:'items', label:'Produto', itemLabel: function(i){ return i.title || 'Novo item'; }, fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'price', label:'Preço', type:'text'},
      {key:'image', label:'Imagem', type:'image'},
      {key:'url', label:'Link (Mercado Livre)', type:'text'}
    ]}},
    instagram: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'subtitle', label:'Subtítulo', type:'text'},
      {key:'url', label:'Link do perfil', type:'text'}
    ]},
    stats: { fields: [], repeat: { key:'items', label:'Número', itemLabel: function(i){ return i.label || 'Novo item'; }, fields: [
      {key:'value', label:'Valor (ex: +15 ou 4.9)', type:'text'},
      {key:'label', label:'Legenda', type:'text'}
    ]}},
    cta: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'text', label:'Texto', type:'textarea'},
      {key:'button', label:'Texto do botão', type:'text'},
      {key:'button_url', label:'Link do botão', type:'text'}
    ]},
    contact: { fields: [
      {key:'title', label:'Título', type:'text'},
      {key:'text', label:'Subtítulo', type:'text'},
      {key:'phone', label:'Telefone/WhatsApp', type:'text'},
      {key:'address', label:'Endereço', type:'textarea'},
      {key:'hours', label:'Horário', type:'textarea'},
      {key:'map', label:'Link do Google Maps (embed)', type:'text'}
    ]}
  };

  var state;
  try { state = JSON.parse(root.dataset.initial || '[]'); } catch (e) { state = []; }
  if (!Array.isArray(state)) state = [];
  state.forEach(function(s){ s.content = s.content || {}; s.settings = s.settings || {}; if (s.active === undefined) s.active = true; });

  function escapeHtml(str) {
    return String(str == null ? '' : str).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; });
  }

  function mediaLink(field) {
    return field.type === 'image' ? ' <a href="{{ route('admin.media.index') }}" target="_blank" class="text-muted">Escolher da mídia &rarr;</a>' : '';
  }

  function fieldHtml(id, field, value) {
    var val = escapeHtml(value);
    var attr = field.type === 'textarea' ? '' : ' value="' + val + '"';
    var input = field.type === 'textarea'
      ? '<textarea id="' + id + '" data-path="' + field.key + '" style="min-height:80px">' + val + '</textarea>'
      : '<input id="' + id + '" data-path="' + field.key + '"' + attr + '>';
    return '<div class="field"><label for="' + id + '">' + field.label + mediaLink(field) + '</label>' + input + '</div>';
  }

  function itemFieldHtml(id, field, value) {
    var val = escapeHtml(value);
    var input = field.type === 'textarea'
      ? '<textarea id="' + id + '" data-item-path="' + field.key + '" style="min-height:60px">' + val + '</textarea>'
      : '<input id="' + id + '" data-item-path="' + field.key + '" value="' + val + '">';
    return '<div class="field"><label for="' + id + '">' + field.label + mediaLink(field) + '</label>' + input + '</div>';
  }

  function renderItems(section, si, schema) {
    if (!schema.repeat) return '';
    var items = section.content[schema.repeat.key] || [];
    var rows = items.map(function(item, ii){
      var fields = schema.repeat.fields.map(function(f){ return itemFieldHtml('if-' + si + '-' + ii + '-' + f.key, f, item[f.key]); }).join('');
      return '<div class="repeat-item" data-item-index="' + ii + '"><div class="repeat-item-head"><strong>' + escapeHtml(schema.repeat.itemLabel(item)) + '</strong><div class="actions">' +
        '<button type="button" class="icon-action" data-action="item-up" title="Mover para cima"><i class="fas fa-arrow-up"></i></button>' +
        '<button type="button" class="icon-action" data-action="item-down" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button>' +
        '<button type="button" class="icon-action" data-action="item-remove" title="Remover"><i class="fas fa-trash"></i></button>' +
        '</div></div>' + fields + '</div>';
    }).join('');
    return '<div class="repeat-list" data-repeat-key="' + schema.repeat.key + '"><h4>' + schema.repeat.label + 's</h4>' +
      (rows || '<p class="text-muted">Nenhum item ainda.</p>') +
      '<button type="button" class="admin-button secondary" data-action="item-add"><i class="fas fa-plus"></i> Adicionar ' + schema.repeat.label.toLowerCase() + '</button></div>';
  }

  function renderSection(section, si) {
    var schema = SCHEMAS[section.type] || { fields: [] };
    var label = LABELS[section.type] || section.type;
    var fields = schema.fields.map(function(f){ return fieldHtml('sf-' + si + '-' + f.key, f, section.content[f.key]); }).join('');
    return '<div class="section-card" data-index="' + si + '"><div class="section-card-head"><span class="badge">' + label + '</span><div class="actions">' +
      '<label class="check"><input type="checkbox" data-action="toggle-active"' + (section.active ? ' checked' : '') + '> Ativa</label>' +
      '<button type="button" class="icon-action" data-action="move-up" title="Mover para cima"><i class="fas fa-arrow-up"></i></button>' +
      '<button type="button" class="icon-action" data-action="move-down" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button>' +
      '<button type="button" class="icon-action" data-action="remove" title="Remover seção"><i class="fas fa-trash"></i></button>' +
      '</div></div><div class="section-card-body">' + fields + renderItems(section, si, schema) + '</div></div>';
  }

  function render() {
    root.innerHTML = state.length ? state.map(renderSection).join('') : '<p class="text-muted">Nenhuma seção ainda. Use "Adicionar seção" abaixo.</p>';
    sync();
  }

  function sync() { hidden.value = JSON.stringify(state); }

  root.addEventListener('input', function(e){
    var card = e.target.closest('.section-card');
    if (!card) return;
    var si = Number(card.dataset.index);
    var section = state[si];
    if (e.target.dataset.path) { section.content[e.target.dataset.path] = e.target.value; sync(); return; }
    if (e.target.dataset.itemPath) {
      var itemEl = e.target.closest('.repeat-item');
      var listEl = e.target.closest('.repeat-list');
      var ii = Number(itemEl.dataset.itemIndex);
      var key = listEl.dataset.repeatKey;
      section.content[key][ii][e.target.dataset.itemPath] = e.target.value;
      sync();
    }
  });

  root.addEventListener('change', function(e){
    if (e.target.dataset.action === 'toggle-active') {
      var card = e.target.closest('.section-card');
      state[Number(card.dataset.index)].active = e.target.checked;
      sync();
    }
  });

  root.addEventListener('click', function(e){
    var btn = e.target.closest('button[data-action]');
    if (!btn) return;
    var card = e.target.closest('.section-card');
    var si = Number(card.dataset.index);
    var action = btn.dataset.action;
    if (action === 'remove') { if (!confirm('Remover esta seção?')) return; state.splice(si, 1); render(); return; }
    if (action === 'move-up' && si > 0) { var a = state[si-1]; state[si-1] = state[si]; state[si] = a; render(); return; }
    if (action === 'move-down' && si < state.length - 1) { var b = state[si+1]; state[si+1] = state[si]; state[si] = b; render(); return; }
    var schema = SCHEMAS[state[si].type] || {};
    if (action === 'item-add' && schema.repeat) {
      var key = schema.repeat.key;
      state[si].content[key] = state[si].content[key] || [];
      var blank = {};
      schema.repeat.fields.forEach(function(f){ blank[f.key] = ''; });
      state[si].content[key].push(blank);
      render();
      return;
    }
    var itemEl = e.target.closest('.repeat-item');
    if (itemEl && schema.repeat) {
      var listEl = e.target.closest('.repeat-list');
      var rkey = listEl.dataset.repeatKey;
      var ii = Number(itemEl.dataset.itemIndex);
      var list = state[si].content[rkey];
      if (action === 'item-remove') { list.splice(ii, 1); render(); return; }
      if (action === 'item-up' && ii > 0) { var c = list[ii-1]; list[ii-1] = list[ii]; list[ii] = c; render(); return; }
      if (action === 'item-down' && ii < list.length - 1) { var d = list[ii+1]; list[ii+1] = list[ii]; list[ii] = d; render(); return; }
    }
  });

  addBtn.addEventListener('click', function(){
    var type = addSelect.value;
    var schema = SCHEMAS[type] || {};
    var content = {};
    (schema.fields || []).forEach(function(f){ content[f.key] = ''; });
    if (schema.repeat) content[schema.repeat.key] = [];
    state.push({ type: type, active: true, content: content, settings: {} });
    render();
  });

  root.closest('form').addEventListener('submit', sync);

  render();
})();
</script>
@endsection
