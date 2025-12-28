<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        // Update logic will be implemented here
        return redirect()->route('admin.settings.index');
    }

    public function plugins()
    {
        return view('admin.plugins.index');
    }

    public function statistics()
    {
        return view('admin.statistics.index');
    }

    public function links()
    {
        return view('admin.links.index');
    }
}
