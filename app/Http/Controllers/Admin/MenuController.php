<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Menu, Page};
use App\Services\SiteCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private readonly SiteCache $cache) {}

    public function edit(): View { $menu = Menu::with('items.page')->firstOrCreate(['location' => 'primary'], ['name' => 'Menu principal']); return view('admin.menus.edit', ['menu' => $menu, 'pages' => Page::orderBy('title')->get()]); }
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'items' => ['nullable', 'array'], 'items.*.label' => ['required', 'string', 'max:100'], 'items.*.url' => ['nullable', 'string', 'max:500'], 'items.*.page_id' => ['nullable', 'exists:pages,id'], 'items.*.target' => ['required', 'in:_self,_blank'], 'items.*.active' => ['nullable', 'boolean']]);
        $menu = Menu::firstOrCreate(['location' => 'primary'], ['name' => $data['name']]); $menu->update(['name' => $data['name']]); $menu->items()->delete();
        foreach ($data['items'] ?? [] as $position => $item) $menu->items()->create(['label' => $item['label'], 'url' => $item['url'] ?? null, 'page_id' => $item['page_id'] ?? null, 'target' => $item['target'], 'position' => $position, 'active' => isset($item['active'])]);
        $this->cache->forgetMenu();
        return back()->with('success', 'Menu salvo.');
    }
}
