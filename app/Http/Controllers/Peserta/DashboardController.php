<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\IngridientStore;
use App\Investation;
use App\Machine;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teams = Auth::user()->team;
        $data_teams = $teams->investations->all();
        $profits = [];

        // Ini data untuk menampilkan Investasi
        foreach ($data_teams as $team_profit) {
            $profits[$team_profit->pivot->investation_id] = $team_profit->pivot->total_profit;
        }

        //dd($teams);

        //Ini data untuk menampilkan data transport_team
        $data_team_transports = $teams->transports->all();
        //dd($data_team_transports);

        //Ini data untuk menampilkan data team_ingridient 
        $data_team_belis = $teams->ingridients->all();
        //dd($data_team_belis);

        //Ini untuk nama toko dari ingridientsnya
        $data_team_storeIngridients = IngridientStore::all();
        //dd($data_team_storeIngridients);

        //Ini data untuk menampilkan data TeamMachine
        $data_team_mesins = TeamMachine::where('team_id', $teams->id)->orderBy('machine_id', 'ASC')->get(); //data utuh lengkap semua yang dimiliki 1 team
        //dd($data_team_mesins);

        //Untuk depresiasi mesinnya
        $hargaMesins = [[]];

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


        //dd($data_team_mesins);

        //Ini data untuk menampilkan data product_team
        $data_team_juals = $teams->products->all();
        //dd($data_team_jual);

        //Nunggu acara untuk rumusnya
        $harga_susun_mesin = 1000;
        $harga_total_susuns = $teams->machine_assembly * $harga_susun_mesin;

        return view('peserta.dashboard.index', compact(
            'teams',
            'data_teams',
            'data_team_transports',
            'data_team_belis',
            'data_team_mesins',
            'hargaMesins',
            'data_team_juals',
            'harga_total_susuns',
            'data_team_storeIngridients'
        ));
    }
}
