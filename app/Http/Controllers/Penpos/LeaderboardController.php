<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Leaderboard;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class LeaderboardController extends Controller
{
    public function index()
    {
        //Ambil semua team
        // $teams = Team::all();
        $teams = DB::select(DB::raw("SELECT * FROM teams"));

        // Ambil variabel waste dari db
        $leaderboard = Leaderboard::find(1);
        // Ubah waste_variable sesuai inputan
        $waste_variabel = $leaderboard->waste_variable;

        $index = 0;
        foreach ($teams as $team) {
            // Ambil Team Machine Udang Kaleng
            // $team_machine_combination = $team->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
            $team_machine_combination = DB::select(DB::raw(
                "SELECT mc.* FROM machine_combinations mc 
                INNER JOIN team_machine_combination tmc ON mc.id = tmc.machine_combination_id 
                INNER JOIN teams t ON tmc.team_id = t.id
                WHERE t.id = $team->id
                AND mc.id != 101
                AND mc.id != 102"
            ));

            // Query semua product team yang dipunya team contoh data nanti [udang kaleng, kitosan, saus]
            $team_product_produced = DB::select(DB::raw(
                "SELECT tp.* FROM product_team tp 
                INNER JOIN products p ON tp.product_id = p.id 
                INNER JOIN teams t ON tp.team_id = t.id
                WHERE t.id = $team->id"
            ));

            // Hitung total product yang pernah dibuat oleh team team
            $total_team_produced = 0;
            foreach ($team_product_produced as $team_product) {
                $total_team_produced += $team_product->amount_have + $team_product->amount_sold;
            }


            // Query semua mesin team yang dipunya saat game selesai
            $team_machine_all = DB::select(DB::raw(
                "SELECT tm.* FROM team_machines tm 
                INNER JOIN machines m ON tm.machine_id = m.id 
                INNER JOIN teams t ON tm.team_id = t.id
                WHERE t.id = $team->id"
            ));

            // Hitung total mesin yang dimiliki team
            $total_mesin_team = count($team_machine_all);

            // Query semua transport team yang dipunya saat game selesai tetapi hanya ngambil 1 tiap jenisnya

            // Lek mau pake ini harus update config/database.php mysql strictnya diset false, mau raw atopun query builder gk bsa
            // Atau dimanual buat having/group by nya itu
            $team_transports = DB::select(DB::raw(
                "SELECT DISTINCT(trans.transport_id) FROM transport_team trans
                INNER JOIN transports tr ON trans.transport_id = tr.id
                INNER JOIN teams t ON trans.team_id = t.id
                WHERE t.id = $team->id"
            ));

            // GROUP BY trans.transport_id
            // HAVING count(trans.transport_id) = 1"
            // // Versi Query buildernya
            // $team_transports = DB::table('transport_team')
            //     ->join('transports', 'transports.id' ,'=','transport_team.transport_id')
            //     ->join('teams', 'teams.id','=','transport_team.team_id')
            //     ->select('transport_team.*')
            //     ->groupBy('transport_team.transport_id')
            //     ->having(DB::raw('count(transport_team.transport_id)'),'=',1)
            //     ->get();

            // Ambil transport_idnya aja gk bsa krn di array_unique itu gk boleh berupa object
            // dilooping buat jadiin array string biasa/angka

            $machine_effectivity = 0;
            $machine_higenity = 0;

            // Set machine effectivity dan higenity
            if ($team_machine_combination != null) {
                $machine_effectivity = $team_machine_combination[0]->effectivity / 100;
                $machine_higenity = $team_machine_combination[0]->higenity / 100;
            }

            // HITUNG SCORE
            // Score Effectivity
            $score_effectivity = ($machine_effectivity / 0.8) * 35;
            $score_higenity = ($machine_higenity / 0.8) * 35;
            // $score_limbah = 30;
            $score_saldo = ($team->tc / 2000) * 30;
            $score_product = $total_team_produced / 3;
            $score_mesin = $total_mesin_team / 17 * 30;
            $score_transport = count($team_transports) / 3 * 20;
            $score_waste = $team->waste / $waste_variabel;

            // // Skenario dibawah 10
            // if ($team->waste <= 10 && $team->waste > 0) {
            //     $score_limbah = 31 - ceil($team->waste);
            // }
            // // Skenario diatas 10
            // else if ($team->waste > 10) {
            //     $score_limbah = 41 - ceil(2 * $team->waste);
            // }

            // if ($team->waste < 0) {
            //     $score_limbah = 0;
            // }

            // if ($score_limbah < 0){
            //     $score_limbah = 0;
            // }

            // SCORE ($score_limbah dihapus)
            $score_level = $score_effectivity + $score_higenity + $score_saldo;
            $score_tambahan = $score_mesin + $score_product + $score_transport;
            // 0 ini perhitungan limbah 
            $score_total = $score_level * 0.6 + $score_tambahan * 0.4 - $score_waste;

            $teams[$index]->higenity = $score_higenity;
            $teams[$index]->effectivity = $score_effectivity;
            $teams[$index]->saldo_akhir = $score_saldo;
            $teams[$index]->score = $score_level;
            $teams[$index]->product = round($score_product, 2);
            $teams[$index]->mesin = round($score_mesin, 2);
            $teams[$index]->transport = round($score_transport, 2);
            $teams[$index]->score_tambahan = round($score_tambahan, 2);
            $teams[$index]->score_total = round($score_total, 2);
            $teams[$index]->waste = $score_waste;

            $index++;
        }
        // dd($teams[0]->effectivity);
        // // Sort berdasarkan score
        array_multisort(array_column($teams, 'score_total'), SORT_DESC, $teams);

        return view('penpos.leaderboard.index', compact('teams'));
    }

    public function waste(Request $request)
    {
        // Ambil inputan variabel waste dari sie Acara
        $waste_variable = $request['waste_variable'];

        // Ambil tabel Leaderboard dari database
        $leaderboard = Leaderboard::find(1);
        // Ubah waste_variable sesuai inputan
        $leaderboard->waste_variable = $waste_variable;
        $leaderboard->save();

        return redirect()->route('penpos.leaderboard');
    }

    public function leaderboardPeserta()
    {
        //Ambil semua team
        // $teams = Team::all();
        $teams = DB::select(DB::raw("SELECT * FROM teams"));

        $index = 0;
        foreach ($teams as $team) {
            // Ambil Team Machine Udang Kaleng
            // $team_machine_combination = $team->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
            $team_machine_combination = DB::select(DB::raw(
                "SELECT mc.* FROM machine_combinations mc 
                INNER JOIN team_machine_combination tmc ON mc.id = tmc.machine_combination_id 
                INNER JOIN teams t ON tmc.team_id = t.id
                WHERE t.id = $team->id
                AND mc.id != 101
                AND mc.id != 102"
            ));

            $machine_effectivity = 0;
            $machine_higenity = 0;

            // Set machine effectivity dan higenity
            if ($team_machine_combination != null) {
                $machine_effectivity = $team_machine_combination[0]->effectivity / 100;
                $machine_higenity = $team_machine_combination[0]->higenity / 100;
            }

            // HITUNG SCORE
            // Score Effectivity
            $score_effectivity = ($machine_effectivity / 0.8) * 35;
            $score_higenity = ($machine_higenity / 0.8) * 35;
            // $score_limbah = 30;
            $score_saldo = ($team->tc / 2000) * 30;

            // // Skenario dibawah 10
            // if ($team->waste <= 10 && $team->waste > 0) {
            //     $score_limbah = 31 - ceil($team->waste);
            // }
            // // Skenario diatas 10
            // else if ($team->waste > 10) {
            //     $score_limbah = 41 - ceil(2 * $team->waste);
            // }

            // if ($team->waste < 0) {
            //     $score_limbah = 0;
            // }

            // if ($score_limbah < 0){
            //     $score_limbah = 0;
            // }

            // SCORE ($score_limbah dihapus)
            $total_score = $score_effectivity + $score_higenity + $score_saldo;

            $teams[$index]->higenity = $machine_higenity;
            $teams[$index]->effectivity = $machine_effectivity;
            $teams[$index]->score = $total_score;

            $index++;
        }

        // // Sort berdasarkan score
        array_multisort(array_column($teams, 'score'), SORT_DESC, $teams);

        return view('penpos.leaderboardteam.index', compact('teams'));
    }
}
