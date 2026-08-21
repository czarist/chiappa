<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\SettingService;
use App\Services\SiteCache;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(private readonly SettingService $settings, private readonly SiteCache $cache) {}
    public function home(): View|Response
    {
        $site = $this->settings->all();
        $slug = $site['home_page'][0] ?? 'inicio';
        $page = $this->cache->page($slug);
        abort_unless($page?->isPublished(), 404);
        return view('pages.show', ['page' => $page, 'site' => $site]);
    }
    public function page(string $slug): View
    {
        $page = $this->cache->page($slug);
        abort_unless($page?->isPublished(), 404);
        return view('pages.show', ['page' => $page, 'site' => $this->settings->all()]);
    }
    public function preview(Page $page): View { abort_unless(auth()->check(), 403); return view('pages.show', ['page' => $page->load(['sections', 'seo']), 'site' => $this->settings->all(), 'preview' => true]); }
    public function sitemap(): Response
    {
        $pages = Page::where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))->get();
        $urls = $pages->map(fn (Page $page) => '<url><loc>'.e(route('pages.show', $page->slug)).'</loc><lastmod>'.$page->updated_at->toAtomString().'</lastmod></url>')->implode('');
        return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>')->header('Content-Type', 'application/xml');
    }
    public function robots(): Response { return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain']); }
}
