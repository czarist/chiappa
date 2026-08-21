<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingService $service) {}
    public function edit(): View { return view('admin.settings.edit', ['settings' => $this->service->all()]); }
    public function update(Request $request): RedirectResponse { $this->service->put($request->except('_token', '_method')); return back()->with('success', 'Configurações salvas.'); }
    public function themes(): View { return view('admin.themes.index', ['themes' => Theme::latest()->get()]); }
    public function activate(Theme $theme): RedirectResponse { Theme::query()->update(['active' => false]); $theme->update(['active' => true]); return back()->with('success', 'Tema ativado.'); }
}
