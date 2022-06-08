<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Investation;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $team = Team::all()->where('id', Auth::user());
        return view('peserta.dashboard.index', compact('team'));
    }

    public function showInvestation(Request $request)
    {
        $data_team = Auth::user()->team->investations;
        return view('peserta.dashboard.index', compact('data_team'));
    }
}
