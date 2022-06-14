<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use App\SeasonNow;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesinController extends Controller
{
    public function index()
    {
        //Declare
        $teams = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machine = TeamMachine::where('team_id', $teams->id)->get();

        // reset selected
        foreach ($team_machine as $mesin) {
            $mesin->selected = 0;
            $mesin->save();
        }

        // Ambil team mesin
        $display_team_mesins = TeamMachine::where('team_id', $teams->id)->where('season_sell', null)->get();

        return view('peserta.mesin.index', compact(
            'teams',
            'display_team_mesins'
        ));
    }

    public function getAvailableMachine()
    {
        // Ambil Team
        $team = Auth::user()->team;
        // Ambil team machine yang not selected dan belum dijual 
        $avaiable_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();

        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($avaiable_machines as $avaiable_machine) {
            $machine = Machine::where('id', $avaiable_machine->machine_id)->first();
            $avaiable_machines[$index]->machine = $machine;
            $index++;
        }

        $status = 'success';

        return response()->json(array(
            'avaiable_machines' => $avaiable_machines,
            'status' => $status,
        ), 200);
    }

    public function setMachine(Request $request)
    {
        // Ambil Team
        $team = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machine = TeamMachine::find($request['team_machine_id']);
        //Ubah selected
        $team_machine->selected = 1;
        $team_machine->save();

        // Ambil team machine yang not selected dan belum dijual 
        $avaiable_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();
        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($avaiable_machines as $avaiable_machine) {
            $machine = Machine::where('id', $avaiable_machine->machine_id)->first();
            $avaiable_machines[$index]->machine = $machine;
            $index++;
        }
        $status = 'success';

        return response()->json(array(
            'avaiable_machines' => $avaiable_machines,
            'status' => $status,
        ), 200);
    }

    public function saveMachine(Request $request)
    {
        //Declare
        $teams = Auth::user()->team;
        // Ambil team machine untuk diubah selectednya
        $team_machine = TeamMachine::where('team_id', $teams->id)->get();

        // reset selected
        foreach ($team_machine as $mesin) {
            $mesin->selected = 0;
            $mesin->save();
        }

        // uang cukup atau tidak?
        if ($teams->tc >= 5) {
            // kurang 5 
            $teams->tc = $teams->tc - 5;
            $teams->total_spend = $teams->total_spend + 5;
        } else { //Tidak cukup uang
            // kurang sesuai tc 
            $teams->tc = 0;
            $teams->total_spend = $teams->total_spend + $teams->tc;
        }
        // total mesin assembly + 1
        $teams->machine_assembly = $teams->machine_assembly + 1;
        $teams->save();

        // Bug : Tambah Terus
        if (!empty($teams->machineCombinations->get())) 
        {
            $teams->machineCombinations->delete();
        }
        $status = '';
        $msg = '';

        // Ambil susunan mesin dari AJAX
        $susunan_mesin = $request['susunan_mesin'];
        // Define Variablex
        $team = Auth::user()->team;

        // Define banyak mesin berapa
        $banyak_machine = count(array_filter($susunan_mesin));;

        // Masukan order dari tiap mesin
        $orders = [];
        foreach ($susunan_mesin as $idx => $machine_id) {
            $orders[$idx + 1] = Machine::find($machine_id);
        }

        // Dapatkan semua kombinasi dari mesin yang berada pada order yang disusun
        $combinations = [];
        for ($i = 1; $i <= $banyak_machine; $i++) {
            $all_combinations = $orders[$i]->machineCombinations()->wherePivot('order', $i)->get();
            $combination_id = [];
            foreach ($all_combinations as $combination) {
                $combination_id[] = $combination->id;
            }
            $combinations[] = $combination_id;
        }

        $combination_found = [];
        if ($banyak_machine > 1) {
            // Lakukan intersect untuk mengetahui apakah ada kombinasi yang cocok
            $combination_found = array_intersect(...$combinations);
        }

        // Apabila terdapat persis satu kombinasi yang cocok maka foundnya true
        $found = (count($combination_found) >= 1) ? true : false;
        // Kombinasi ada
        if ($found) {
            // Hapus kombinasi kecuali kombinasi kitosan  dan saus tomat

            // Ambil Kombinasi
            if (count($combination_found) > 1) {
                //Ini set di team_machine_combination
                $combination = MachineCombination::find($combination_found[0]);
                $team->machineCombinations()->attach($combination->id);
            } else {
                //Ini set di team_machine_combination
                $combination = MachineCombination::find($combination_found);
                $team->machineCombinations()->attach($combination[0]->id);
            }
            // Update tambahkan machine combination
            $team->save();

            $status = 'success';
            $msg = 'Kombinasi yang dimasukkan sudah benar';
        } else {
            $status = 'error';
            $msg = 'Kombinasi yang dimasukkan belum tepat!';
        }

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function sellMachine(Request $request)
    {
        $status = '';
        $msg = '';

        // Define Variable
        $team = Auth::user()->team;
        $team_machine = TeamMachine::find($request['team_machine_id']);
        $season_sell = SeasonNow::first()->number;
        $season_buy = $team_machine->season_buy;
        $price_var = $team_machine->machine->price_var;
        $buy_price = $team_machine->machine->price;

        // Perhitungan Harga jual
        $dT = ($buy_price - $price_var) / 3;
        $sell_price = round($buy_price - ($season_sell * $dT), 2);
        //Tambah uang
        $team->tc = $team->tc + $sell_price;
        $team->total_income = $team->total_income + $sell_price;
        $team->save();

        //update season sell team machine (jual)
        $team_machine->season_sell = $season_sell;
        $team_machine->save();
        $status = "success";

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
