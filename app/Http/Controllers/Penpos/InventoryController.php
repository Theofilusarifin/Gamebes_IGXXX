<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Ingridient;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Team $team)
    {
        //Declare
        $teams = Team::all();
        $team_products = $team->products->where('pivot.amount_have', '>', '0')->all();
        $team_ingridients = $team->ingridients->where('pivot.amount_have', '>', '0')->all();

        return view('si.inventory.index', compact(
            'teams',
            'team',
            'team_products',
            'team_ingridients'
        ));
    }

    public function ingridientExpired(Request $request)
    {
        // Ambil data dari AJAX
        $team_id =  $request['team_id'];
        $team =  Team::find($team_id);
        $ingridient_id = $request['ingridient_id'];
        $expired_time = $request['expired_time'];

        $ingridient = Ingridient::find($ingridient_id);

        $ingridient_team = DB::table('ingridient_team')
        ->where('team_id', $team_id)
            ->where('ingridient_id', $ingridient_id)
            ->where('expired_time', $expired_time)
            ->first();

        // Pastikan ingridient belum terhapus
        if ($ingridient_team != null) {
            $team->waste = $team->waste + $ingridient_team->amount_have;
            $team->save();

            // Hapus data di database
            DB::table('ingridient_team')
            ->where('team_id', $team_id)
                ->where('ingridient_id', $ingridient_id)
                ->where('expired_time', $expired_time)
                ->delete();
        }

        // Message untuk response
        $msg = $ingridient->name . " yang dimiliki telah expired!";

        return response()->json(array(
            'status' => 'error',
            'msg' => $msg,
        ), 200);
    }
}
