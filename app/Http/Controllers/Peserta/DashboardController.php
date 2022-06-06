<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Team;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('peserta.dashboard.index');
    }
}
