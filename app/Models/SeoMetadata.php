<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoMetadata extends Model
{
    protected $fillable = [
        'page_id', 'title', 'description', 'keywords', 'author', 'canonical_url',
        'indexable', 'followable', 'og_title', 'og_description', 'og_image', 'og_image_alt',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
    ];

    protected function casts(): array
    {
        return ['indexable' => 'boolean', 'followable' => 'boolean'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function resolved(Page $page, array $site = []): array
    {
        $config = $site['site'] ?? [];
        $title = $this->title ?: $page->title;
        $description = $this->description ?: ($config['description'] ?? '');
        $canonical = $this->canonical_url ?: url()->current();
        $siteName = $config['name'] ?? config('app.name');
        $ogImage = $this->absoluteUrl($this->og_image ?: $page->featured_image ?: ($config['logo'] ?? null));
        $twitterImage = $this->absoluteUrl($this->twitter_image) ?: $ogImage;

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->keywords,
            'author' => $this->author ?: $siteName,
            'canonical' => $canonical,
            'robots' => ($this->indexable === false ? 'noindex' : 'index').','.($this->followable === false ? 'nofollow' : 'follow').($this->indexable === false ? '' : ',max-image-preview:large'),
            'og_type' => 'website',
            'og_locale' => 'pt_BR',
            'og_site_name' => $siteName,
            'og_url' => $canonical,
            'og_title' => $this->og_title ?: $title,
            'og_description' => $this->og_description ?: $description,
            'og_image' => $ogImage,
            'og_image_alt' => $this->og_image_alt ?: $title,
            'twitter_card' => $this->twitter_card ?: 'summary_large_image',
            'twitter_title' => $this->twitter_title ?: ($this->og_title ?: $title),
            'twitter_description' => $this->twitter_description ?: ($this->og_description ?: $description),
            'twitter_image' => $twitterImage,
            'favicon' => $config['favicon'] ?? asset('assets/favicon.svg'),
            'theme_color' => '#c8102e',
            'json_ld' => $this->jsonLd($page, $config, $title, $description, $canonical, $ogImage),
        ];
    }

    private function jsonLd(Page $page, array $config, string $title, string $description, string $canonical, ?string $image): array
    {
        $address = strip_tags(str_replace(['<br>', '<br/>', '<br />'], ', ', $config['address'] ?? ''));

        $business = [
            '@context' => 'https://schema.org',
            '@type' => 'AutoPartsStore',
            'name' => $config['name'] ?? 'Chiappa Auto Peças e Serviços',
            'description' => $config['description'] ?? $description,
            'url' => $config['url'] ?? url('/'),
            'telephone' => $config['phone'] ?? null,
            'email' => $config['email'] ?? null,
            'image' => $this->absoluteUrl($config['logo'] ?? '/assets/logo.png'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $address ?: 'Rua Cristóvão Colombo, 33',
                'addressLocality' => 'Canoas',
                'addressRegion' => 'RS',
                'postalCode' => '92110-450',
                'addressCountry' => 'BR',
            ],
        ];

        if (! empty($config['instagram'])) {
            $business['sameAs'] = [$config['instagram']];
        }

        $webpage = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => 'pt-BR',
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $config['name'] ?? config('app.name'),
                'url' => $config['url'] ?? url('/'),
            ],
            'dateModified' => optional($page->updated_at)->toAtomString(),
        ];

        if ($image) {
            $webpage['primaryImageOfPage'] = $image;
        }

        return [$business, $webpage];
    }

    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
    }
}
