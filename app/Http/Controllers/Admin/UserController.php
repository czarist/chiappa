<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        return view('admin.users.index', [
            'users' => User::latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('admin.users.form', ['user' => new User(['role' => 'editor', 'status' => 'active'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();
        $data = $this->validated($request);
        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado.');
    }

    public function edit(User $user): View
    {
        $this->authorizeAdmin();

        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();
        $data = $this->validated($request, $user);

        if ($user->is($request->user()) && ($data['status'] !== 'active' || $data['role'] !== 'administrator')) {
            return back()->withErrors(['user' => 'Você não pode remover o próprio acesso de administrador.']);
        }

        if ($user->isAdministrator() && ($data['role'] !== 'administrator' || $data['status'] !== 'active') && $this->activeAdministratorCount($user) === 0) {
            return back()->withErrors(['user' => 'Mantenha pelo menos um administrador ativo.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Você não pode excluir o próprio usuário.']);
        }

        if ($user->isAdministrator() && $this->activeAdministratorCount($user) === 0) {
            return back()->withErrors(['user' => 'Mantenha pelo menos um administrador ativo.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'password' => $passwordRules,
            'role' => ['required', 'in:administrator,editor'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function activeAdministratorCount(?User $except = null): int
    {
        return User::query()
            ->where('role', 'administrator')
            ->where('status', 'active')
            ->when($except, fn ($query) => $query->where('id', '!=', $except->id))
            ->count();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdministrator(), 403);
    }
}
