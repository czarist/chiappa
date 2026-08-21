<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create(): Response
    {
        return response()->view('admin.auth.login')->header(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0'
        );
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
        }

        if (! $request->user()->isActive()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Usuário inativo.']);
        }

        $request->session()->regenerate();

        return redirect()->away($this->intendedAdminPath($request));
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('login', absolute: false));
    }

    private function intendedAdminPath(Request $request): string
    {
        $dashboard = route('admin.dashboard', absolute: false);
        $intended = $request->session()->pull('url.intended', $dashboard);
        $path = parse_url($intended, PHP_URL_PATH) ?: $dashboard;
        $query = parse_url($intended, PHP_URL_QUERY);
        $prefix = rtrim($dashboard, '/');

        if ($path !== $prefix && ! str_starts_with($path, $prefix.'/')) {
            return $dashboard;
        }

        return $path.($query ? '?'.$query : '');
    }
}
