<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use App\Services\MediaService;
use App\Services\PageService;
use App\Services\SiteCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly PageService $service, private readonly MediaService $media, private readonly SiteCache $cache) {}
    public function index(): View { return view('admin.pages.index', ['pages' => Page::with('seo')->latest()->paginate(12)]); }
    public function create(): View { return view('admin.pages.form', ['page' => new Page(['status' => 'draft', 'template' => 'default']), 'sectionTypes' => $this->types()]); }
    public function store(PageRequest $request): RedirectResponse { $page = $this->save($request); return redirect()->route('admin.pages.edit', $page)->with('success', 'Página criada.'); }
    public function edit(Page $page): View { return view('admin.pages.form', ['page' => $page->load(['sections', 'seo']), 'sectionTypes' => $this->types()]); }
    public function update(PageRequest $request, Page $page): RedirectResponse { $this->save($request, $page); return back()->with('success', 'Página atualizada.'); }
    public function destroy(Page $page): RedirectResponse
    {
        $slug = $page->slug;
        $page->delete();
        $this->cache->forgetPage($slug);

        return redirect()->route('admin.pages.index')->with('success', 'Página excluída.');
    }
    public function publish(Page $page): RedirectResponse { $this->service->publish($page); return back()->with('success', 'Página publicada.'); }
    public function duplicate(Page $page): RedirectResponse { $copy = $this->service->duplicate($page); return redirect()->route('admin.pages.edit', $copy)->with('success', 'Página duplicada.'); }
    private function save(PageRequest $request, ?Page $page = null): Page
    {
        $data = $request->validated(); $sections = json_decode($data['sections_json'] ?? '[]', true) ?: []; unset($data['sections_json'], $data['sections']);
        $page = $this->service->save($data, $page); $page->sections()->delete();
        foreach ($sections as $position => $section) $page->sections()->create(['type' => $section['type'] ?? 'content', 'position' => $position, 'active' => (bool)($section['active'] ?? true), 'content' => $this->media->localizeContent($section['content'] ?? [], $request->user()), 'settings' => $section['settings'] ?? []]);
        $this->cache->forgetPage($page->slug);
        return $page;
    }
    private function types(): array { return ['hero' => 'Hero', 'differentials' => 'Diferenciais', 'services' => 'Serviços', 'products' => 'Vitrine', 'instagram' => 'Instagram', 'stats' => 'Números', 'cta' => 'CTA', 'contact' => 'Contato']; }
}
