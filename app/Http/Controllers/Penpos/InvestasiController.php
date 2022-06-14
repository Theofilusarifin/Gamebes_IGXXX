<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Investation;
use App\Team;
use Illuminate\Http\Request;

class InvestasiController extends Controller
{
    public function index(){
        $teams = Team::all();
        $investations = Investation::all();
        return view('penpos.investasi.index', compact('investations', 'teams'));
    }

    public function save(Request $request){
        $team = Team::find($request['team_id']);
        $investation = Investation::find($request['investation_id']);
        $nilai_investasi = $request['nilai_investasi'];

        // Status dan message untuk respond
        $status = 'success';
        $msg = 'Nilai Investasi berhasil diperbarui';

        $team->investations()->sync([$investation->id => [
            'total_profit' => $nilai_investasi
        ]], false);

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
