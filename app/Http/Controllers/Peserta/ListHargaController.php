<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\Product;
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
        $index = 0;
        foreach ($ingridients as $key => $ingridient) {
            $total_stock = $ingridient->ingridientStores->sum('pivot.stock');
            $ingridients[$index]->stock = $total_stock;
            $index++;
        }

        $machines = Machine::all();
        $index = 0;
        foreach ($machines as $key => $machine) {
            $total_stock = $machine->machineStores->sum('pivot.stock');
            $machines[$index]->stock = $total_stock;
            $index++;
        }

        $transports = Transport::all();
        $index = 0;
        foreach ($transports as $key => $transport) {
            $total_stock = $transport->transportStores->sum('pivot.stock');
            $transports[$index]->stock = $total_stock;
            $index++;
        }

        $services = Service::first();
        $service_stock = Service::sum('stock');
        $services['total_stock'] = $service_stock;
        //dd($services);
        return view('peserta.harga.index', compact('season_now', 'products', 'ingridients', 'machines', 'transports', 'services'));
    }
}
