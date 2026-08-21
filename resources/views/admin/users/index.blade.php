@extends('layouts.admin')
@section('title', 'Usuários')
@section('content')
<div class="page-heading">
    <div>
        <h1>Usuários</h1>
        <p class="text-muted">Quem pode entrar no CMS. Editores alteram conteúdo; administradores gerenciam acessos.</p>
    </div>
    <a class="admin-button" href="{{ route('admin.users.create') }}"><i class="fas fa-plus"></i> Novo usuário</a>
</div>
<div class="admin-card">
    <table class="admin-table">
        <tr>
            <th>Usuário</th>
            <th>Papel</th>
            <th>Status</th>
            <th>Criado</th>
            <th></th>
        </tr>
        @forelse($users as $user)
            <tr>
                <td>
                    <div class="user-cell">
                        <span class="admin-avatar">{{ $user->initials() }}</span>
                        <div>
                            <strong>{{ $user->name }}</strong>
                            <small>{{ $user->email }}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge {{ $user->role }}">{{ $user->roleLabel() }}</span></td>
                <td><span class="badge {{ $user->status }}">{{ $user->statusLabel() }}</span></td>
                <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                <td class="actions">
                    <a class="icon-action" href="{{ route('admin.users.edit', $user) }}" title="Editar"><i class="fas fa-pen"></i></a>
                    @unless($user->is(auth()->user()))
                        <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Excluir este usuário?')">
                            @csrf @method('DELETE')
                            <button class="icon-action danger" type="submit" title="Excluir"><i class="fas fa-trash"></i></button>
                        </form>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Nenhum usuário cadastrado.</td></tr>
        @endforelse
    </table>
    {{ $users->links() }}
</div>
@endsection
