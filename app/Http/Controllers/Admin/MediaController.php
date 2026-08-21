<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View { return view('admin.media.index', ['media' => Media::latest()->paginate(24)]); }
    public function store(MediaUploadRequest $request): RedirectResponse
    {
        $file = $request->file('file'); $path = $file->store('media', 'public');
        Media::create(['user_id' => $request->user()->id, 'name' => $file->getClientOriginalName(), 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'alt_text' => $request->input('alt_text')]);
        return back()->with('success', 'Imagem enviada.');
    }
    public function destroy(Media $medium): RedirectResponse { Storage::disk('public')->delete($medium->path); $medium->delete(); return back()->with('success', 'Mídia excluída.'); }
}
