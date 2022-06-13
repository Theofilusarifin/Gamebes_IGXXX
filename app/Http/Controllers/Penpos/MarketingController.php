<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Team;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function index(){
        $teams = Team::all();
        return view('penpos.marketing.index', compact('teams'));
    }
}
