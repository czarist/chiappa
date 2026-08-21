<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\{RedirectResponse, Response};
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
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
        if (! $request->user()->isActive()) { Auth::logout(); return back()->withErrors(['email' => 'Usuário inativo.']); }
        $request->session()->regenerate();
        $intendedUrl = $request->session()->pull('url.intended', route('admin.dashboard', absolute: false));
        $path = parse_url($intendedUrl, PHP_URL_PATH) ?: $intendedUrl;
        $query = parse_url($intendedUrl, PHP_URL_QUERY);
        return redirect()->away($path.($query === null ? '' : '?'.$query));
    }
    public function destroy(): RedirectResponse { Auth::logout(); request()->session()->invalidate(); request()->session()->regenerateToken(); return redirect(route('login', absolute: false)); }
}
