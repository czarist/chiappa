<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;

class PageService
{
    public function __construct(private readonly SiteCache $cache) {}

    public function save(array $data, ?Page $page = null): Page
    {
        $page ??= new Page();
        $previousSlug = $page->exists ? $page->getOriginal('slug') : null;
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        $page->fill($data)->save();
        $page->seo()->updateOrCreate([], $data['seo'] ?? []);
        $this->cache->forgetPage($page->slug, $previousSlug);

        return $page->fresh(['sections', 'seo']);
    }

    public function publish(Page $page): Page
    {
        $page->update(['status' => 'published', 'published_at' => now()]);
        $this->cache->forgetPage($page->slug);

        return $page;
    }

    public function duplicate(Page $page): Page
    {
        $copy = $page->replicate();
        $copy->title .= ' (cópia)';
        $copy->slug = Str::slug($copy->title).'-'.Str::lower(Str::random(5));
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->save();
        foreach ($page->sections as $section) {
            $copy->sections()->create($section->only(['type', 'position', 'active', 'content', 'settings']));
        }
        if ($page->seo) {
            $copy->seo()->create($page->seo->only(['title', 'description', 'keywords', 'author', 'canonical_url', 'indexable', 'followable', 'og_title', 'og_description', 'og_image', 'og_image_alt', 'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image']));
        }

        return $copy;
    }
}
