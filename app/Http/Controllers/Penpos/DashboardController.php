<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\SeasonNow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('penpos.dashboard.index');
    }

    public function updateSeason()
    {
        return view('penpos.updateSeason.index');
    }

    public function updateNow()
    {
        $status = '';
        $msg = '';

        // Update Season
        $seasonNow = SeasonNow::first();
        $seasonNow->id = $seasonNow->number + 1;
        $seasonNow->number = $seasonNow->number + 1;
        $seasonNow->save();

        // Refresh Stock
        DB::statement("UPDATE `services` SET stock = 2");
        DB::statement("UPDATE `ingridient_ingridient_store` SET stock = 5");
        DB::statement("UPDATE `machine_machine_store` SET stock = 5");
        DB::statement("UPDATE `transport_transport_store` SET stock = 3");

        $status = 'success';
        $msg = 'Season berhasil di update';

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
