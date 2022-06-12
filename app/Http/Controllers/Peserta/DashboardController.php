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

        $data_teams = "";
        $data_team_transports = "";
        $data_team_belis = "";
        $data_team_mesins = "";
        $data_team_juals = "";
        $harga_total_susuns = "";
        $data_team_storeIngridients = "";
        if (!empty($teams->investations->all())) {
            $data_teams = $teams->investations->all();
            $profits = [];
            // Ini data untuk menampilkan Investasi
            foreach ($data_teams as $team_profit) {
                $profits[$team_profit->pivot->investation_id] = $team_profit->pivot->total_profit;
            }
        }

        //dd($teams);

        //Ini data untuk menampilkan data transport_team
        if (!empty($teams->transports->all())) {
            $data_team_transports = $teams->transports->all();
        }

        //dd($data_team_transports);
        $table_store = array(
            "Udang Vaname" => "Seafood Store",
            "Udang Pama" => "Seafood Store",
            "Udang Jerbung" => "Seafood Store",
            "Tomat" => "Tomat Store",
            "Air Mineral" => "Kelontong Store",
            "Garam" => "Kelontong Store",
            "Gula" => "Kelontong Store",
            "MSG" => "Kelontong Store",
            "NaOH" => "Chemical Store",
            "HCl" => "Chemical Store"
        );

        //Ini data untuk menampilkan data pembelian SUDAH URUT!
        if (!empty($teams->ingridients->all())) {
            $data_team_belis = $teams->ingridients->all();
        }
        //dd($data_team_belis);
        //d($table_store["Udang Vaname"]);
        //Ini untuk nama toko dari ingridientsnya
        if (!empty($teams->ingridients->all())) {
            $toko_barang_teams = [[]]; 
            $counter = -1;
            for ($i=0; $i < count($data_team_belis) ; $i++) {
                $nama_barang = $data_team_belis[$i]->name;
                if(!in_array($table_store[$nama_barang],$toko_barang_teams))
                {
                    $counter+=1;
                    $toko_barang_teams[$counter][] = $table_store[$nama_barang];
                }
                else{
                    $toko_barang_teams[$counter][] = $table_store[$nama_barang];
                }
            }
        }
        dd($toko_barang_teams);

        //Ini data untuk menampilkan data TeamMachine
        if (!empty(TeamMachine::where('team_id', $teams->id)->orderBy('machine_id', 'ASC')->get())) {
            $data_team_mesins = TeamMachine::where('team_id', $teams->id)->orderBy('machine_id', 'ASC')->get(); //data utuh lengkap semua yang dimiliki 1 team
        }
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
        if (!empty($teams->products->all())) {
            $data_team_juals = $teams->products->all();
        }
        //dd($data_team_jual);

        //Nunggu acara untuk rumusnya
        $harga_total_susuns = $teams->machine_assembly * 5;


        return view('peserta.dashboard.index', compact(
            'teams',
            'data_teams',
            'data_team_transports',
            'data_team_belis',
            'data_team_mesins',
            'hargaMesins',
            'data_team_juals',
            'harga_total_susuns',
            'data_team_storeIngridients',
            'toko_barang_teams'
        ));
    }
}
