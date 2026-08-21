<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\MercadoLivreService;
use App\Services\SettingService;
use App\Services\SiteCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingService $service, private readonly SiteCache $cache) {}
    public function edit(MercadoLivreService $mercadoLivre): View { return view('admin.settings.edit', ['settings' => $this->service->all(), 'mlConnected' => $mercadoLivre->isConnected()]); }
    public function update(Request $request): RedirectResponse { $this->service->put($request->except('_token', '_method')); return back()->with('success', 'Configurações salvas.'); }
    public function themes(): View { return view('admin.themes.index', ['themes' => Theme::latest()->get()]); }
    public function activate(Theme $theme): RedirectResponse
    {
        Theme::query()->update(['active' => false]);
        $theme->update(['active' => true]);
        $this->cache->forgetSettings();

        return back()->with('success', 'Tema ativado.');
    }

    public function clearCache(): RedirectResponse
    {
        $this->cache->flush();

        return back()->with('success', 'Cache do site limpo.');
    }
}
