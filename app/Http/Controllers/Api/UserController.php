<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Users list retrieved successfully',
            'data' => [],
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'User details retrieved successfully',
            'data' => ['id' => $id],
        ]);
    }
}
