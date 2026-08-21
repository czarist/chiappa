<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index() { return response()->json(['success' => true, 'data' => PageResource::collection(Page::with(['sections', 'seo'])->where('status', 'published')->latest()->paginate(20)), 'message' => null]); }
    public function show(Page $page) { abort_unless($page->isPublished(), 404); return response()->json(['success' => true, 'data' => new PageResource($page->load(['sections', 'seo'])), 'message' => null]); }
    public function store(Request $request) { abort_unless($request->user()?->isAdministrator(), 403); $data = $request->validate(['title' => 'required|string|max:150', 'slug' => 'nullable|string|max:180', 'status' => 'in:draft,published,scheduled']); $page = Page::create($data); return response()->json(['success' => true, 'data' => new PageResource($page), 'message' => 'Page created'], 201); }
    public function update(Request $request, Page $page) { abort_unless($request->user()?->isAdministrator(), 403); $page->update($request->validate(['title' => 'sometimes|string|max:150', 'slug' => 'sometimes|string|max:180', 'status' => 'sometimes|in:draft,published,scheduled'])); return response()->json(['success' => true, 'data' => new PageResource($page->fresh()), 'message' => 'Page updated']); }
    public function destroy(Request $request, Page $page) { abort_unless($request->user()?->isAdministrator(), 403); $page->delete(); return response()->json(['success' => true, 'data' => null, 'message' => 'Page deleted']); }
}
