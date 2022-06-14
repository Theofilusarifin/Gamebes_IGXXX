<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\Season;
use App\SeasonNow;
use App\Service;
use App\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListHargaController extends Controller
{
    public function index()
    {
        $season_now = Season::find(SeasonNow::first()->id);
        $products = $season_now->products->all();
        $ingridients = $season_now->ingridients->all();
        $machines = Machine::all();
        $transports = Transport::all();
        $services = Service::all();
        return view('peserta.harga.index', compact('season_now', 'products', 'ingridients', 'machines', 'transports', 'services'));
    }
}
