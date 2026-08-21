<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Media, Page, User};
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', ['stats' => ['pages' => Page::count(), 'published' => Page::where('status', 'published')->count(), 'drafts' => Page::where('status', 'draft')->count(), 'users' => User::count(), 'media' => Media::count()], 'latest' => Page::latest('updated_at')->limit(5)->get()]);
    }
}
