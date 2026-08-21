<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View { abort_unless(auth()->user()->isAdministrator(), 403); return view('admin.users.index', ['users' => User::latest()->paginate(20)]); }
    public function create(): View { abort_unless(auth()->user()->isAdministrator(), 403); return view('admin.users.create'); }
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:150', 'unique:users,email'], 'password' => ['required', 'string', 'min:8', 'confirmed'], 'role' => ['required', 'in:administrator,editor'], 'status' => ['required', 'in:active,inactive']]);
        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Usuário criado.');
    }
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isAdministrator(), 403); $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'role' => ['required', 'in:administrator,editor'], 'status' => ['required', 'in:active,inactive']]);
        if ($user->is(auth()->user()) && ($data['status'] !== 'active' || $data['role'] !== 'administrator')) return back()->withErrors(['user' => 'Você não pode remover seu próprio acesso administrativo.']);
        $user->update($data); return back()->with('success', 'Usuário atualizado.');
    }
    public function destroy(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isAdministrator(), 403);
        if ($user->is(auth()->user())) return back()->withErrors(['user' => 'Você não pode excluir seu próprio usuário.']);
        $user->delete();
        return back()->with('success', 'Usuário excluído.');
    }
}
