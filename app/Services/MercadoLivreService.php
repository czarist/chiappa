<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MercadoLivreService
{
    private const TOKEN_URL = 'https://api.mercadolibre.com/oauth/token';

    public function __construct(private readonly SiteCache $cache) {}

    public function authorizationUrl(string $redirectUri, string $state, string $codeChallenge): string
    {
        return 'https://auth.mercadolivre.com.br/authorization?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.mercadolivre.client_id'),
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
    }

    public function exchangeCode(string $code, string $redirectUri, string $codeVerifier): void
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.mercadolivre.client_id'),
            'client_secret' => config('services.mercadolivre.client_secret'),
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'code_verifier' => $codeVerifier,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao trocar o código de autorização: '.$response->body());
        }

        $this->storeToken($response->json());
    }

    public function isConnected(): bool
    {
        return ! empty($this->tokenData()['refresh_token'] ?? null);
    }

    public function fetchSellerItems(): array
    {
        $accessToken = $this->getValidAccessToken();
        $sellerId = config('services.mercadolivre.seller_id');

        $ids = [];
        $offset = 0;

        do {
            $response = Http::withToken($accessToken)->get("https://api.mercadolibre.com/users/{$sellerId}/items/search", [
                'status' => 'active',
                'limit' => 50,
                'offset' => $offset,
            ]);

            if (! $response->successful()) {
                throw new RuntimeException('Falha ao listar itens do vendedor: '.$response->body());
            }

            $page = $response->json('results', []);
            $ids = array_merge($ids, $page);
            $offset += 50;
            $total = $response->json('paging.total', 0);
        } while ($offset < $total && $offset < 200);

        if (empty($ids)) {
            return [];
        }

        $products = [];

        foreach (array_chunk($ids, 20) as $chunk) {
            $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/items', [
                'ids' => implode(',', $chunk),
                'attributes' => 'id,title,price,thumbnail,permalink,pictures',
            ]);

            if (! $response->successful()) {
                throw new RuntimeException('Falha ao buscar detalhes dos itens: '.$response->body());
            }

            foreach ($response->json() as $entry) {
                $item = $entry['body'] ?? null;
                if (! $item) {
                    continue;
                }

                $products[] = [
                    'title' => $item['title'] ?? '',
                    'price' => 'R$ '.number_format((float) ($item['price'] ?? 0), 2, ',', '.'),
                    'image' => $item['pictures'][0]['secure_url'] ?? $item['thumbnail'] ?? null,
                    'url' => $item['permalink'] ?? null,
                ];
            }
        }

        return $products;
    }

    private function getValidAccessToken(): string
    {
        $token = $this->tokenData();

        if (empty($token['refresh_token'])) {
            throw new RuntimeException('Mercado Livre não conectado. Autorize o aplicativo no admin primeiro.');
        }

        if (($token['expires_at'] ?? 0) > time() + 60) {
            return $token['access_token'];
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.mercadolivre.client_id'),
            'client_secret' => config('services.mercadolivre.client_secret'),
            'refresh_token' => $token['refresh_token'],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao renovar o token do Mercado Livre: '.$response->body());
        }

        $this->storeToken($response->json());

        return $this->tokenData()['access_token'];
    }

    private function storeToken(array $payload): void
    {
        Setting::updateOrCreate(['key' => 'ml_oauth'], ['value' => [
            'access_token' => $payload['access_token'],
            'refresh_token' => $payload['refresh_token'] ?? $this->tokenData()['refresh_token'] ?? null,
            'expires_at' => time() + ($payload['expires_in'] ?? 21600),
            'user_id' => $payload['user_id'] ?? null,
        ]]);
        $this->cache->forgetSettings();
    }

    private function tokenData(): array
    {
        return Setting::where('key', 'ml_oauth')->first()?->value ?? [];
    }

    public static function generateCodeVerifier(): string
    {
        return Str::random(64);
    }

    public static function codeChallengeFor(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }
}
