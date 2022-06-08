<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('penpos.map.index');
    }
}
