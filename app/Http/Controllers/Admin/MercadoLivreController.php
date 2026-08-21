<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MercadoLivreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MercadoLivreController extends Controller
{
    public function __construct(private readonly MercadoLivreService $service) {}

    public function connect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $codeVerifier = MercadoLivreService::generateCodeVerifier();

        $request->session()->put('ml_oauth_state', $state);
        $request->session()->put('ml_oauth_verifier', $codeVerifier);

        $url = $this->service->authorizationUrl(
            route('admin.mercadolivre.callback'),
            $state,
            MercadoLivreService::codeChallengeFor($codeVerifier)
        );

        return redirect($url);
    }

    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->pull('ml_oauth_state');
        $codeVerifier = $request->session()->pull('ml_oauth_verifier');

        if (! $request->filled('code') || ! $state || $request->query('state') !== $state) {
            return redirect()->route('admin.settings.edit')->with('error', 'Falha na autorização do Mercado Livre. Tente novamente.');
        }

        try {
            $this->service->exchangeCode($request->query('code'), route('admin.mercadolivre.callback'), $codeVerifier);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.edit')->with('error', 'Erro ao conectar com o Mercado Livre: '.$e->getMessage());
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Mercado Livre conectado com sucesso!');
    }
}
