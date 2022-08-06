<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Machine;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(){
        $teams = Team::all();
        return view('penpos.maintenance.index', compact('teams'));
    }

    public function getTeamMachine(Request $request){
        $team_id = $request['team_id'];
        $team_machines = TeamMachine::where('team_id', $team_id)->where('season_sell', null)->get();

        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($team_machines as $team_machine) {
            $machine = Machine::where('id', $team_machine->machine_id)->first();
            $team_machines[$index]->machine = $machine;
            $index++;
        }

        return response()->json(
            array(
                'team_machines' => $team_machines,
            ),
            200
        );
    }

    public function save(Request $request){
        //Inisiasi Variabel
        $team = Team::find($request['team_id']);
        $team_machine = TeamMachine::find($request['team_machine_id']);
        $nilai_maintenance = $request['nilai_maintenance'];

        $msg = '';
        $status = '';
        
        //Cek Performa Mesin apakah masih bisa melakukan maintenance
        if($team_machine->performance <= 30){
            $status = 'error';
            $msg = 'Performance mesin terlalu kecil untuk melakukan maintenance!';

            return response()->json(
                array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
        }

        $total_price = 0;
        // Logic harga maintenance
        if($nilai_maintenance == 25){
            $total_price = 15;
        }
        else if ($nilai_maintenance == 50) {
            $total_price = 20;
        }
        else if ($nilai_maintenance == 75) {
            $total_price = 25;
        }
        else{
            $status = 'error';
            $msg = 'Persentase maintenance tidak valid!';

            return response()->json(
                array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
        }

        // Uang tidak cukup
        if ($team->tc < $total_price){
            $status = 'error';
            $msg = 'Tiggie Coin yang dimiliki tidak cukup untuk melakukan Maintenance Mesin!';
        }

        else{
            // Tambahkan performance dari mesin
            $team_machine->performance = $team_machine->performance + $nilai_maintenance;
            if ($team_machine->performance > 100) $team_machine->performance = 100;
            $team_machine->save();

            // Update status team
            $team->tc = $team->tc - $total_price;
            $team->total_spend = $team->total_spend + $total_price;
            $team->total_maintenance = $team->total_maintenance+1;
            $team->save();

            $status = 'success';
            $msg = 'Maintenance berhasil dilakukan';
        }
        return response()->json(
            array(
                'status' => $status,
                'msg' => $msg,
            ),
            200
        );
    }
}