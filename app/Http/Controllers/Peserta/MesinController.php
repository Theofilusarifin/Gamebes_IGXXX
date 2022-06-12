<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesinController extends Controller
{
    public function index()
    {
        //Declare
        $teams = Auth::user()->team;
        $data_team_mesins = "";
        $hargaMesins = "";

        //Untuk depresiasi mesinnya
        $hargaMesins = [[]];

        if (!empty(TeamMachine::where('team_id', $teams->id)->orderBy('machine_id', 'ASC')->get())) {
            $data_team_mesins = TeamMachine::where('team_id', $teams->id)->orderBy('machine_id', 'ASC')->get(); //data utuh lengkap semua yang dimiliki 1 team
        }

        //dd($data_team_mesins);

        for ($i = 0; $i < count($data_team_mesins); $i++) {
            //CEK MESINNYA UDAH DIJUAL ATAU BELUM
            for ($j = 0; $j <= 16; $j++) {
                if ($data_team_mesins->machine_id == $j && $data_team_mesins->season_sell >= 0) {
                    //lanjut proses
                    $waktuJual = $data_team_mesins->season_sell + 1;
                    $kumpulanHargaJualDasar = Machine::where('id', $data_team_mesins[$i]->machine_id)->get(['price_var']); //Ini harga jual dasar
                    $kumpulanHargaBeli = Machine::where('id', $data_team_mesins[$i]->machine_id)->get(['price']);
                    //Ada kemungkinan satu team punya 2 mesin yang sama jadi dilooping
                    for ($k = 0; $k < count($kumpulanHargaJualDasar); $k++) {
                        //Dimasukin ke array 2D
                        //Jadi i itu adalah banyaknya mesin yang dimiliki oleh team tertentu
                        //Sedangkan j itu banyaknya mesin spesifik yang dimiliki oleh team tertentu
                        $hargaJualDasar = $kumpulanHargaJualDasar[$k];
                        $hargaBeli = $kumpulanHargaBeli[$k];
                        $dT = ($hargaBeli - $hargaJualDasar) / 3;
                        $hargaMesins[$j][$k] = $hargaBeli - ($waktuJual * $dT);
                        //tinggal tunggu acara untuk rumusnya
                    }
                } else {
                    $hargaMesins[$j][] = 0;
                }
            }
        }

        return view('peserta.mesin.index', compact(
            'teams',
            'data_team_mesins',
            'hargaMesins',
        ));
    }

    public function SusunMesin(Request $request)
    {
        // Define banyak mesin berapa
        $banyak_machine = count($request->all());

        // Masukan order dari tiap mesin
        $orders = [];
        foreach ($request as $idx => $machine) {
            $orders[$idx + 1] = $machine;
        }

        // Dapatkan semua kombinasi dari mesin yang berada pada order yang disusun
        $combinations = [];
        for ($i = 1; $i <= $banyak_machine; $i++) {
            $combinations[] = $orders[$i]->machineCombinations()->withPivot('order', $i)->get(['id']);
        }

        // Lakukan intersect untuk mengetahui apakah ada kombinasi yang cocok
        $combination_found = array_intersect(...$combinations);

        // Apabila terdapat persis satu kombinasi yang cocok maka statusnya true
        $status = (count($combination_found) == 1) ? true : false;

        // 
        if ($status) {
            $combination = MachineCombination::find($combination_found[1]);
        }

        return response()->json([
            'status' => $status,
            'combination' => $combination,
        ]);
    }
}
