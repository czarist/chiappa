<?php

use App\Http\Controllers\Api\{PageController, SettingController};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('pages', [PageController::class, 'index']);
    Route::get('pages/{page}', [PageController::class, 'show']);
    Route::get('settings', SettingController::class);
    Route::get('menus', fn () => response()->json(['success' => true, 'data' => \App\Models\Menu::with('items')->get(), 'message' => null]));
    Route::get('media', fn () => response()->json(['success' => true, 'data' => \App\Models\Media::latest()->paginate(30), 'message' => null]));
    Route::middleware('auth')->group(function () {
        Route::post('pages', [PageController::class, 'store']);
        Route::put('pages/{page}', [PageController::class, 'update']);
        Route::delete('pages/{page}', [PageController::class, 'destroy']);
    });
});
