<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Investation;
use App\Team;
use Illuminate\Http\Request;

class InvestasiController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        $investations = Investation::all();
        return view('penpos.investasi.index', compact('investations', 'teams'));
    }

    public function save(Request $request)
    {
        $team = Team::find($request['team_id']);
        $investation = Investation::find($request['investation_id']);
        $nilai_investasi = $request['nilai_investasi'];
        // Status dan message untuk respond
        $status = 'success';
        $msg = 'Nilai Investasi berhasil diperbarui';

        //update total_profit
        $team->investations()->sync([$investation->id => [
            'total_profit' => $nilai_investasi
        ]], false);
        //update total_income
        //update team_tcnya salah!
        $team->total_income = $team->total_income + $nilai_investasi;
        $team->tc = $team->tc + $nilai_investasi;
        $team->save();
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
