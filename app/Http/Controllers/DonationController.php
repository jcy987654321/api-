<?php

namespace App\Http\Controllers;

use App\Models\DonationOption;

class DonationController extends Controller
{
    public function index()
    {
        $donations = DonationOption::active()->get();
        return view('donations.index', compact('donations'));
    }
}
