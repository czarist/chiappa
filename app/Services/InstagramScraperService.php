<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstagramScraperService
{
    private const APP_ID = '936619743392459';

    public function fetchProfile(string $username): ?array
    {
        $response = Http::withHeaders([
            'x-ig-app-id' => self::APP_ID,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept' => '*/*',
            'Accept-Language' => 'pt-BR,pt;q=0.9,en;q=0.8',
            'Referer' => "https://www.instagram.com/{$username}/",
        ])->timeout(15)->get('https://www.instagram.com/api/v1/users/web_profile_info/', [
            'username' => $username,
        ]);

        if (! $response->successful()) {
            Log::warning("InstagramScraperService: falha ao buscar @{$username} (HTTP {$response->status()})");

            return null;
        }

        $user = $response->json('data.user');

        if (! $user) {
            Log::warning("InstagramScraperService: payload inesperado para @{$username}");

            return null;
        }

        $posts = collect($user['edge_owner_to_timeline_media']['edges'] ?? [])
            ->take(6)
            ->map(function (array $edge) {
                $node = $edge['node'] ?? [];
                $caption = $node['edge_media_to_caption']['edges'][0]['node']['text'] ?? '';

                return [
                    'image' => $node['display_url'] ?? null,
                    'permalink' => isset($node['shortcode']) ? 'https://www.instagram.com/p/'.$node['shortcode'].'/' : null,
                    'likes' => $node['edge_liked_by']['count'] ?? $node['edge_media_preview_like']['count'] ?? 0,
                    'comments' => $node['edge_media_to_comment']['count'] ?? 0,
                    'caption' => Str::limit(trim(strip_tags($caption)), 140),
                ];
            })
            ->filter(fn (array $post) => ! empty($post['image']))
            ->values()
            ->all();

        return [
            'username' => $username,
            'followers' => $user['edge_followed_by']['count'] ?? 0,
            'following' => $user['edge_follow']['count'] ?? 0,
            'posts_count' => $user['edge_owner_to_timeline_media']['count'] ?? 0,
            'profile_pic' => $user['profile_pic_url_hd'] ?? $user['profile_pic_url'] ?? null,
            'posts' => $posts,
            'scraped_at' => now()->toIso8601String(),
        ];
    }
}
