<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelController extends Controller
{
    public function index()
    {
        //Deklarasi
        $team = Auth::user()->team;

        return view('peserta.level.index', compact(
            'team',
        ));
    }
}
