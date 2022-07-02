<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Ingridient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        //Declare
        $team = Auth::user()->team;
        $team_products = $team->products->where('pivot.amount_have', '>', '0')->all();
        $team_ingridients = $team->ingridients->where('pivot.amount_have', '>', '0')->all();

        return view('peserta.inventory.index', compact(
            'team',
            'team_products',
            'team_ingridients'
        ));
    }

    public function ingridientExpired(Request $request){
        // Ambil data dari AJAX
        $team = Auth::user()->team;
        $ingridient_id = $request['ingridient_id'];
        $expired_time = $request['expired_time'];

        $ingridient = Ingridient::find($ingridient_id);

        // Hapus data di database
        DB::table('ingridient_team')
        ->where('team_id', $team->id)
        ->where('ingridient_id', $ingridient_id)
        ->where('expired_time', $expired_time)
        ->delete();

        // Message untuk response
        $msg = $ingridient->name. " yang dimiliki telah expired!";

        return response()->json(array(
            'status' => 'error',
            'msg' => $msg,
        ),200);
    }
}
