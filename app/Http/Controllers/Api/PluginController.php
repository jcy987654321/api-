<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PluginController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Plugins list retrieved successfully',
            'data' => [],
        ]);
    }
}
