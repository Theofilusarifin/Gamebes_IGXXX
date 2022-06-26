<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use App\SeasonNow;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MesinController extends Controller
{
    public function index()
    {
        //Declare
        $teams = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machines = TeamMachine::where('team_id', $teams->id)->where('season_sell', null)->get();

        // reset selected
        foreach ($team_machines as $team_machine) {
            $team_machine->selected = 0;
            $team_machine->save();
        }

        // Ambil machine combination sebagai default dari combobox
        //101 --> saus tomat, 102 --> kitosan
        $machine_combination_udang = $teams->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
        $machine_combination_saus = $teams->machineCombinations->where("id", "101")->first();
        $machine_combination_kitosan = $teams->machineCombinations->where("id", "102")->first();

        //Deklarasi untuk nampung efektivitas dari kombinasi yang dipakai
        $machine_udang_tersimpan = "";
        $machine_saus_tersimpan = "";
        $machine_kitosan_tersimpan = "";

        if ($machine_combination_udang != null) {
            $machine_udang_tersimpan = $machine_combination_udang->machines->sortBy('pivot.order');
        }
        if ($machine_combination_saus != null) {
            $machine_saus_tersimpan = $machine_combination_saus->machines->sortBy('pivot.order');
        }
        if ($machine_combination_kitosan != null) {
            $machine_kitosan_tersimpan = $machine_combination_kitosan->machines->sortBy('pivot.order');
        }

        return view('peserta.mesin.index', compact(
            'teams',
            'team_machines',
            'machine_combination_udang',
            'machine_combination_saus',
            'machine_combination_kitosan',
            'machine_udang_tersimpan',
            'machine_saus_tersimpan',
            'machine_kitosan_tersimpan'
        ));
    }

    public function getAvailableMachine()
    {
        // Ambil Team
        $team = Auth::user()->team;
        // Ambil team machine yang not selected dan belum dijual 
        $available_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();

        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($available_machines as $available_machine) {
            $machine = Machine::where('id', $available_machine->machine_id)->first();
            $available_machines[$index]->machine = $machine;
            $index++;
        }

        $status = 'success';

        return response()->json(array(
            'available_machines' => $available_machines,
            'status' => $status,
        ), 200);
    }

    public function setMachine(Request $request)
    {
        // Ambil Team
        $team = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machine = $team->teamMachines->where('id', $request['team_machine_id'])->first();

        //Ubah selected
        $team_machine->selected = 1;
        $team_machine->save();

        // Ambil team machine yang not selected dan belum dijual 
        $available_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();
        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($available_machines as $available_machine) {
            $machine = Machine::where('id', $available_machine->machine_id)->first();
            $available_machines[$index]->machine = $machine;
            $index++;
        }
        $status = 'success';

        return response()->json(array(
            'available_machines' => $available_machines,
            'status' => $status,
        ), 200);
    }

    public function saveMachine(Request $request)
    {
        //Declare
        $team = Auth::user()->team;
        // Ambil team machine untuk diubah selectednya
        $team_machines = TeamMachine::where('team_id', $team->id)->get();

        // reset selected
        foreach ($team_machines as $team_machine) {
            $team_machine->selected = 0;
            $team_machine->save();
        }

        //Tidak cukup uang
        if ($team->tc < 5) {
            // kurang sesuai tc 
            $team->tc = 0;
            $team->total_spend = $team->total_spend + $team->tc;
            $status = "error";
            $msg = "Tiggie coin anda tidak cukup untuk melakukan penyusunan mesin!";
            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila cukup tc kurangi 5 
        $team->tc = $team->tc - 5;
        $team->total_spend = $team->total_spend + 5;

        // total mesin assembly + 1
        $team->machine_assembly = $team->machine_assembly + 1;
        $team->save();

        // Ambil tipenya
        $tipe = $request['tipe'];

        // Menghapus kombinasi lama untuk diganti kombinasi baru
        if ($tipe == "udang") {
            //101 --> saus tomat, 102 --> kitosan
            DB::table('team_machine_combination')
                ->where('machine_combination_id', "!=", 101)
                ->where('machine_combination_id', "!=", 102)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($tipe = "saus") {
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 101)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($tipe = "kitosan") {
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 102)
                ->where('team_id', $team->id)
                ->delete();
        }

        $status = '';
        $msg = '';

        // Ambil susunan mesin dari AJAX
        $susunan_mesin = $request['susunan_mesin']; //array yang berisikan team_machine_id [1,2,3,4]

        // Define banyak mesin berapa (buang yang isinya null menggunakan array filter)
        $banyak_machine = count(array_filter($susunan_mesin));

        if ($banyak_machine >= 4) {
            // Masukan order dari tiap mesin
            $orders = [];

            for ($i = 0; $i < $banyak_machine; $i++) {
                // dd($susunan_mesin[$i]);
                // Cari machine_idnya dulu pakai team_machine_id
                $tm = TeamMachine::find($susunan_mesin[$i]);
                // Masukkan machine ke dalam order
                $orders[$i + 1] = Machine::find($tm->machine_id);
            }

            // Dapatkan semua kombinasi dari mesin yang berada pada order yang disusun
            $combinations = [];
            for ($i = 1; $i <= $banyak_machine; $i++) {
                $all_combinations = $orders[$i]->machineCombinations()->wherePivot('order', $i)->get();
                // dd($all_combinations);
                $combination_id = [];
                foreach ($all_combinations as $combination) {
                    $combination_id[] = $combination->id;
                }
                $combinations[] = $combination_id;
            }
            // dd($combinations);
            $combination_found = [];
            if ($banyak_machine > 1) {
                // Lakukan intersect untuk mengetahui apakah ada kombinasi yang cocok
                $combination_found = array_intersect(...$combinations);
            }
            // dd($combination_found);
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
                $msg = 'Kombinasi yang dimasukkan sudah benar! Kombinasi akan disimpan.';
            } else {
                $status = 'error';
                $msg = 'Kombinasi yang dimasukkan belum tepat! Kombinasi tidak akan disimpan.';
                //Semisal team sudah pernah benar kemudian coba kombinasi baru dan salah maka kombinasi lama akan hilang.
            }
        } else {
            $status = 'error';
            $msg = 'Kombinasi yang dimasukkan belum tepat! Kombinasi tidak akan disimpan.';
        }

        //Deklarasi untuk nampung efektivitas dari kombinasi yang dipakai
        $machine_combination_udang = $team->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
        $machine_combination_saus = $team->machineCombinations->where("id", "101")->first();
        $machine_combination_kitosan = $team->machineCombinations->where("id", "102")->first();

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
            'machine_combination_saus' => $machine_combination_saus,
            'machine_combination_kitosan' => $machine_combination_kitosan,
            'machine_combination_udang' => $machine_combination_udang,
        ), 200);
    }

    public function sellMachine(Request $request)
    {
        $status = '';
        $msg = '';

        // Define Variable
        $team = Auth::user()->team;
        $team_machine = TeamMachine::find($request['team_machine_id']);
        $season_sell = SeasonNow::first()->number; //ambil season sekarang dan simpan di season_sell
        $season_buy = $team_machine->season_buy; //ambil season beli dari mesin
        $price_var = $team_machine->machine->price_var; //ambil harga jual
        $buy_price = $team_machine->machine->price; //ambil harga beli

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

        // Ambil team mesin
        $team_mesins = TeamMachine::where('team_id', $team->id)->where('season_sell', null)->get();

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
            'team_mesins' => $team_mesins,
        ), 200);
    }
}
