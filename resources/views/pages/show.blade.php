@extends('layouts.app')
@section('content')
@if(!empty($preview))<div class="preview-bar">Visualização de rascunho <a href="{{ route('admin.pages.edit', $page) }}">Voltar ao editor</a></div>@endif
@foreach($page->sections->where('active', true) as $section)@includeIf('components.site.sections.'.$section->type, ['content' => $section->content ?? [], 'section' => $section])@endforeach
@endsection
