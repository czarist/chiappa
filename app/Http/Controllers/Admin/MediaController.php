<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaFromUrlRequest;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(): View
    {
        return view('admin.media.index', ['media' => Media::latest()->paginate(24)]);
    }

    public function store(MediaUploadRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $item = $this->media->storeUpload($request->file('file'), $request->user(), $request->input('alt_text'));
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['file' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => ['id' => $item->id, 'url' => $item->url(), 'name' => $item->name]]);
        }

        return back()->with('success', 'Imagem enviada.');
    }

    public function storeFromUrl(MediaFromUrlRequest $request): JsonResponse
    {
        try {
            $item = $this->media->storeFromUrl($request->string('url')->toString(), $request->user(), $request->input('alt_text'));
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => ['id' => $item->id, 'url' => $item->url(), 'name' => $item->name]]);
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->media->deleteFile($medium);
        $medium->delete();

        return back()->with('success', 'Mídia excluída.');
    }
}
