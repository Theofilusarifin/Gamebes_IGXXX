<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('penpos.dashboard.index');
    }
}
