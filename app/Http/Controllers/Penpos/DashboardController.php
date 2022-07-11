<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Machine;
use App\SeasonNow;
use App\Service;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('penpos.dashboard.index');
    }

    public function updateSeason()
    {
        return view('si.updateSeason.index');
    }

    public function updateNow()
    {
        $status = '';
        $msg = '';

        // Update Season
        $seasonNow = SeasonNow::first();
        $seasonNow->id = $seasonNow->number + 1;
        $seasonNow->number = $seasonNow->number + 1;
        $seasonNow->save();

        // Refresh Stock
        DB::statement("UPDATE `services` SET stock = 2");
        DB::statement("UPDATE `ingridient_ingridient_store` SET stock = 5");
        DB::statement("UPDATE `machine_machine_store` SET stock = 5");
        DB::statement("UPDATE `transport_transport_store` SET stock = 3");

        $status = 'success';
        $msg = 'Season berhasil di update';

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
    
    public function getDataTeam(Team $team)
    {
        $teams = Team::all();

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

        return view('penpos.dashboard.peserta', compact(
            'teams',
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
