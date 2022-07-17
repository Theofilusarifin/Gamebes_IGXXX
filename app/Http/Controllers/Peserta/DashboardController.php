<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\IngridientStore;
use App\Investation;
use App\Machine;
use App\SeasonNow;
use App\Service;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        //Deklarasi
        $team = Auth::user()->team;

        // Pembelian
        $team_ingridients = $team->ingridients->all();
        $team_machines2 = TeamMachine::where('team_id', $team->id)->get();

        $index = 0;
        foreach ($team_machines2 as $team_mesin) {
            $mesin = Machine::where('id', $team_mesin->machine_id)->first();
            $nama_mesin = $team_mesin->machine->name;
            $team_machines2[$index]->name = $nama_mesin;
            $index++;
        }

        $team_transports = $team->transports->all();
        $team_services = Service::find($team->service_id);

        // Penjualan
        $team_products = $team->products->where('pivot.amount_sold', '>', '0')->all();

        // Investasi
        $team_investations = $team->investations->all();

        // Penjualan Mesin
        $team_machines = TeamMachine::where('team_id', $team->id)->where('season_sell', '!=', null)->get();

        // Masukkan detail machine kedalam array Penjualan machine
        $counter = 0;
        foreach ($team_machines as $team_machine) {
            // ambil mesin
            $machine = Machine::where('id', $team_machine->machine_id)->first();

            // spesifikasi season
            $season_buy = $team_machine->season_buy;
            $season_sell = $team_machine->season_sell;

            // Variable yang dibutuhkan
            $price_var = $team_machine->machine->price_var;
            $buy_price = $team_machine->machine->price;
            $nama_machine = $team_machine->machine->name;

            // rumus DT
            $dT = ($buy_price - $price_var) / 3;
            $sell_price = round($buy_price - ($season_sell * $dT), 2);

            $team_machines[$counter]->sell_price = $sell_price;
            $team_machines[$counter]->name = $nama_machine;
            $counter++;
        }
        $arraySeason = array(1 => "Musim Panas", 2 => "Musim Hujan", 3 => "Musim Salju");

        // dd($team_products);
        return view('peserta.dashboard.index', compact(
            'team',
            'team_ingridients',
            'team_machines',
            'team_machines2',
            'team_transports',
            'team_services',
            'team_products',
            'team_investations',
            'team_machines',
            'arraySeason'
        ));
    }
}
