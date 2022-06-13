<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\IngridientStore;
use App\Investation;
use App\Machine;
use App\SeasonNow;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        //Deklarasi
        $teams = Auth::user()->team;
        $data_teams = "";
        $data_team_transports = "";
        $data_team_belis = "";
        $data_team_mesins = "";
        $data_team_juals = "";
        $harga_total_susuns = "";
        $data_team_storeIngridients = "";
        $toko_barang_teams = "";
        $profits = array();

        //Cek apakah investasinya ada atau tidak?
        if (!empty($teams->investations->all())) {
            $data_teams = $teams->investations->all();
            // Ini data untuk menampilkan Investasi
            foreach ($data_teams as $team_profit) {
                $profits[$team_profit->pivot->investation_id] = $team_profit->pivot->total_profit;
            }
        }
        //Hardcode table_store buat cek
        $table_store = array(
            "Udang Vaname" => 0,
            "Udang Pama" => 0,
            "Udang Jerbung" => 0,
            "Tomat" => 1,
            "Air Mineral" => 2,
            "Garam" => 2,
            "Gula" => 2,
            "MSG" => 2,
            "NaOH" => 3,
            "HCl" => 3
        );
        //Hardcode untuk nama storenya
        $table_store2 = array("Seafood Store", "Tomat Store", "Kelontong Store", "Chemical Store");

        //Ini data untuk menampilkan data pembelian SUDAH URUT!
        if (!empty($teams->ingridients->all())) {
            $data_team_belis = $teams->ingridients->all();
        }
        //dd($data_team_belis);

        //Ini untuk nama toko dari ingridientsnya -->harus 2D
        if (!empty($teams->ingridients->all())) {
            $toko_barang_teams = array(0 => array(), 1 => array(), 2 => array(), 3 => array());
            for ($i = 0; $i < count($data_team_belis); $i++) {
                $nama_barang = $data_team_belis[$i]->name; //Udang Vaname, Udang Pama, Tomat, MSG
                $nama_toko = $table_store[$nama_barang];
                $jumlah = $data_team_belis[$i]->pivot->amount_have;
                $total = $data_team_belis[$i]->pivot->total;
                $toko_barang_teams[$nama_toko][] = $nama_barang;
                $toko_barang_teams[$nama_toko][] = $jumlah;
                $toko_barang_teams[$nama_toko][] = $total;
            }
        }
        //dd($toko_barang_teams);

        //Ini data untuk menampilkan data TeamMachine
        // if (!empty($teams->teamMachines->all())) {
        //     $data_team_mesins = $teams->teamMachines->all();
        //     $counter = 0;
        //     foreach ($data_team_mesins as $spesifikMesin) {
        //         if ($spesifikMesin->season_sell >= 1) {
        //             //lanjut proses
        //             $spesifikMesinData = Machine::where('id', $spesifikMesin->machine_id)->get();
        //             $hargaJualDasar = $spesifikMesinData[0]->price_var; //harga jual dasar
        //             $hargaBeliDasar = $spesifikMesinData[0]->price; //harga beli
        //             $dT = ($hargaBeliDasar - $hargaJualDasar) / 3;
        //             $hargaJualMesin = round($hargaBeliDasar - ($spesifikMesin->season_sell * $dT), 2);
        //             $hargaMesins[$counter] = $spesifikMesinData[0]->name;
        //             $seasonBeli = $spesifikMesin->season_buy;
        //             if ($seasonBeli == 1) {
        //                 $namaSeasonBeli = "Panas";
        //             } elseif ($seasonBeli == 2) {
        //                 $namaSeasonBeli = "Hujan";
        //             } else {
        //                 $namaSeasonBeli = "Salju";
        //             }
        //             $seasonJual = $spesifikMesin->season_sell;
        //             if ($seasonJual == 1) {
        //                 $namaSeasonJual = "Panas";
        //             } elseif ($seasonJual == 2) {
        //                 $namaSeasonJual = "Hujan";
        //             } else {
        //                 $namaSeasonJual = "Salju";
        //             }
        //             $hargaMesins[$counter + 1] = $namaSeasonBeli;
        //             $hargaMesins[$counter + 2] = $namaSeasonJual;
        //             $hargaMesins[$counter + 3] = $hargaJualMesin;
        //             $counter += 4;
        //         }
        //     }
        // }

        //Tampilin Penjualan Mesin
        $hargaMesins = [];
        $counter = 0;
        $arraySeason = array(1 => "Musim Panas", 2 => "Musim Hujan", 3 => "Musim Salju");
        $team_machine = $teams->teamMachines->all();
        foreach ($team_machine as $mesinku) {
            $season_sell = $mesinku->season_sell;
            //CEK udah KEJUAL APA BLM
            if ($season_sell >= 1) {
                $season_buy = $mesinku->season_buy;
                $price_var = $mesinku->machine->price_var;
                $buy_price = $mesinku->machine->price;
                $spesifikMesinData = Machine::where('id', $mesinku->machine_id)->get();

                // Perhitungan Harga jual
                $dT = ($buy_price - $price_var) / 3;
                $sell_price = round($buy_price - ($season_sell * $dT), 2);

                // Masukin Array buat tampilin
                $hargaMesins[$counter] = $spesifikMesinData[0]->name;
                $hargaMesins[$counter + 1] = $arraySeason[$season_buy];
                $hargaMesins[$counter + 2] = $arraySeason[$season_sell];
                $hargaMesins[$counter + 3] = $sell_price;
                $counter += 4;
            }
        }

        if (!empty($teams->products->all())) {
            $data_team_juals = $teams->products->all();
        }
        //dd($data_team_juals);


        return view('peserta.dashboard.index', compact(
            'teams',
            'data_teams',
            'data_team_transports',
            'data_team_belis',
            'data_team_mesins',
            'hargaMesins',
            'data_team_juals',
            'data_team_storeIngridients',
            'profits',
            'table_store',
            'table_store2',
            'toko_barang_teams'
        ));
    }
}
