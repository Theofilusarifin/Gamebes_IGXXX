<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        //Declare
        $teams = Auth::user()->team;
        $data_team_belis = "";
        $data_team_juals = "";
        $toko_barang_teams = "";
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
        $table_store2 = array("Seafood Store", "Tomat Store", "Kelontong Store", "Chemical Store");

        if (!empty($teams->ingridients->all())) {
            $data_team_belis = $teams->ingridients->all();
        }

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

        if (!empty($teams->products->all())) {
            $data_team_juals = $teams->products->all();
        }


        return view('peserta.inventory.index', compact(
            'teams',
            'data_team_belis',
            'data_team_juals',
            'table_store',
            'table_store2',
            'toko_barang_teams'
        ));
    }
}
