<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function stats()
    {
        return [
            'users' => 0,
            'orders' => 0,
            'revenue' => 0,
        ];
    }
}
