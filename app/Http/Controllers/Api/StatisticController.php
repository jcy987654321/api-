<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [],
        ]);
    }
}
