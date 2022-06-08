<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Territory;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $territories = Territory::all();
        return view('penpos.map.index', compact('territories'));
    }
}
