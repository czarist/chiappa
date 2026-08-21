@extends('layouts.admin')
@section('title', $user->exists ? 'Editar usuário' : 'Novo usuário')
@section('content')
<div class="page-heading">
    <div>
        <h1>{{ $user->exists ? 'Editar usuário' : 'Novo usuário' }}</h1>
        <p class="text-muted">{{ $user->exists ? 'Altere dados, papel, status ou senha.' : 'Crie um acesso para a equipe.' }}</p>
    </div>
    <a class="admin-button secondary" href="{{ route('admin.users.index') }}">Voltar</a>
</div>
<div class="form-card" style="max-width:560px">
    <form method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif
        <div class="field">
            <label for="name">Nome</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="field">
            <label for="password">{{ $user->exists ? 'Nova senha (opcional)' : 'Senha' }}</label>
            <input id="password" name="password" type="password" minlength="8" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
        </div>
        <div class="field">
            <label for="password_confirmation">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
        </div>
        <div class="field">
            <label for="role">Papel</label>
            <select id="role" name="role">
                <option value="editor" @selected(old('role', $user->role) === 'editor')>Editor</option>
                <option value="administrator" @selected(old('role', $user->role) === 'administrator')>Administrador</option>
            </select>
        </div>
        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" @selected(old('status', $user->status ?: 'active') === 'active')>Ativo</option>
                <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inativo</option>
            </select>
        </div>
        <div class="form-actions">
            <button class="admin-button" type="submit">{{ $user->exists ? 'Salvar alterações' : 'Criar usuário' }}</button>
        </div>
    </form>
</div>
@endsection
