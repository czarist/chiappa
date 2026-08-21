<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function __invoke(SettingService $settings) { return response()->json(['success' => true, 'data' => $settings->all(), 'message' => null]); }
}
