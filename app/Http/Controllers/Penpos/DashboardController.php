<?php

namespace App\Http\Controllers\Penpos;

use App\Events\UpdateSeason;
use App\Http\Controllers\Controller;
use App\Machine;
use App\Season;
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
        $response = 'error';

        // Update Season
        $seasonNow = SeasonNow::first(); //1

        if ($seasonNow->number < 3) {
            $past_season = Season::where('number', $seasonNow->number)->first();
            if ($seasonNow->number == 1 && $past_season->updated == 0) {
                $next_season = $past_season;
            } else {
                $next_season = Season::where('number', $seasonNow->number + 1)->first();
            }
            // Waktu di Surabaya sekarang
            $now = date('Y-m-d H:i:s');

            // Check apakah season belum terupdate dan apakah sudah waktunya melakukan update
            if (!$next_season->updated && $past_season->end_time <= $now) {
                // Update Season
                $seasonNow->number = $next_season->number;
                $seasonNow->name = $next_season->name;
                $seasonNow->save();

                // Waktu di Surabaya sekarang
                $now = date('Y-m-d H:i:s');
                // Tambah 20 menit waktu di surabaya sekarang
                $season_end = date(
                    'Y-m-d H:i:s',
                    strtotime('+20 minutes', strtotime($now))
                );
                // Set waktu untuk season selanjutnya
                DB::statement("UPDATE `seasons` SET start_time = '$now', end_time = '$season_end' , updated = 1 WHERE number = $next_season->number");

                // Refresh Stock
                DB::statement("UPDATE `services` SET stock = 2");
                DB::statement("UPDATE `ingridient_ingridient_store` SET stock = 5");
                DB::statement("UPDATE `machine_machine_store` SET stock = 5");
                DB::statement("UPDATE `transport_transport_store` SET stock = 3");

                //Kalau musim hujan semua mesin sealer yang dimiliki semua team akan dihapus
                if ($seasonNow->number == 2) {
                    //Ambil semua mesin sealer yang ada dan belum terjual
                    DB::statement("DELETE FROM `team_machines` WHERE machine_id = 8 AND season_sell IS NULL");
                }

                $response = 'success';
            }
        }

        $status = 'success';
        $msg = 'Season ' . SeasonNow::first()->name . ' telah dimulai!';
        
        // Tambahi keterangan mesin sealer rusak
        if ($seasonNow->number == 2) {
            $msg += " Seluruh mesin sealer telah rusak!";
        }

        if ($response != 'error') event(new UpdateSeason($msg));
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function getDataTeam(Team $team)
    {
        $teams = Team::all();
        // dd($team->total_income);
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
