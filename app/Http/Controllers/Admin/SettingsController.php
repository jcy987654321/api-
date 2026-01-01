<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function plugins()
    {
        return view('admin.plugins.index');
    }

    public function pluginSettings()
    {
        return view('admin.plugins.settings');
    }

    public function accessStatistics()
    {
        return view('admin.statistics.access');
    }

    public function apiStatistics()
    {
        return view('admin.statistics.api');
    }

    public function userStatistics()
    {
        return view('admin.statistics.users');
    }
}