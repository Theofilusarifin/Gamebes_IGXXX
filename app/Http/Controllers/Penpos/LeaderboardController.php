<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
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
                $machine_effectivity = $team_machine_combination[0]->effectivity;
                $machine_higenity = $team_machine_combination[0]->higenity;
            }

            // HITUNG SCORE
            // Score Effectivity
            $score_effectivity = ($machine_effectivity / 0.8) * 25;
            $score_higenity = ($machine_higenity / 0.8) * 25;
            $score_limbah = 30;
            $score_saldo = ($team->tc / 2000) * 20;

            // Skenario dibawah 10
            if ($team->waste <= 10 && $team->waste > 0) {
                $score_limbah = 31 - ceil($team->waste);
            }
            // Skenario diatas 10
            else if ($team->waste > 10) {
                $score_limbah = 41 - ceil(2 * $team->waste);
            }

            if ($team->waste < 0) {
                $score_limbah = 0;
            }

            // SCORE
            $total_score = $score_effectivity + $score_higenity + $score_limbah + $score_saldo;
            if ($total_score > 100) {
                $total_score = 100;
            }

            $teams[$index]->higenity = $machine_higenity;
            $teams[$index]->effectivity = $machine_effectivity;
            $teams[$index]->score = $total_score;

            $index++;
        }

        // // Sort berdasarkan score
        array_multisort(array_column($teams, 'score'), SORT_DESC, $teams);

        return view('penpos.leaderboard.index', compact('teams'));
    }
}
