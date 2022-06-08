<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
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
        $team = Auth::user()->team;
        $data_team = $team->investations->all();
        $profit = [];

        // Ini data untuk menampilkan Investasi
        foreach ($data_team as $team) {
            $profit[$team->pivot->investation_id] = $team->pivot->total_profit;
        }
        // var_dump($var);
        //dd($var);

        //Ini data untuk menampilkan data team transport belum masuk data rawnya
        $team = Auth::user()->team;
        $data_team_transport = $team->transports;
        //dd($data_team_transport);

        //Ini data untuk menampilkan data team beli belum masuk data rawnya
        $team = Auth::user()->team;
        $data_team_beli = $team->ingridients->all();
        //dd($data_team_beli);

        //Ini data untuk menampilkan data team mesin belum masuk data rawnya
        $team = Auth::user()->team;
        $data_team_mesin = TeamMachine::where('team_id', $team->id)->get();
        dd($data_team_mesin);

        //Untuk harga jual mesinnya
        $hargajualMesin = 0;

        //Ini data untuk menampilkan data team jual
        $team = Auth::user()->team;
        $data_team_jual = $team
        return view('peserta.dashboard.index', compact(
            'team',
            'data_team',
            'data_team_transport',
            'data_team_beli',
            'data_team_mesin',
            'hargajualMesin'
        ));
    }
}
