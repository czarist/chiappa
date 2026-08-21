<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\SettingService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}
    public function home(): View|Response
    {
        $slug = $this->settings->all()['home_page'][0] ?? 'inicio';
        $page = Page::with(['sections', 'seo'])->where('slug', $slug)->first();
        abort_unless($page?->isPublished(), 404);
        return view('pages.show', ['page' => $page, 'site' => $this->settings->all()]);
    }
    public function page(string $slug): View
    {
        $page = Cache::remember('page:'.$slug, 300, fn () => Page::with(['sections', 'seo'])->where('slug', $slug)->firstOrFail());
        abort_unless($page->isPublished(), 404); return view('pages.show', ['page' => $page, 'site' => $this->settings->all()]);
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
