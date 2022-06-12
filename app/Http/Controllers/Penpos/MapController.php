<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Territory;
use App\SeasonNow;
use App\Team;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $territories = Territory::all();
        $teams = Team::all();
        return view('penpos.map.index', compact('territories', 'teams'));
    }

    public function spawn(Request $request){
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($request['territory_id']);
        $season_now = SeasonNow::first();

        // Status dan message yang diberikan
        $response = '';
        $status = '';
        $msg = '';

        // Kalau penpos sudah select team
        if (isset($team)) {
            // Kalau team memang belum berada pada suatu territory (Territory idnya null)
            if (!isset($team->territory_id)) {
                // Kalau territory yang dipilih adalah territory valid
                if (isset($territory)) {
                    // Territory yang dipilih adalah daerah spawn
                    if ($territory->is_harbour) {
                        // Update territory team ke territory spawn point
                        $team->territory_id = $territory->id;
                        // Kalau salju berkurang 60
                        if ($season_now->number = 3){
                            $team->poin_gambes = $team->poin_gambes - 60 <= 0 ? 0 :  $team->poin_gambes - 60;
                        }
                        // Kalau panas hujan 30
                        else{
                            $team->poin_gambes = $team->poin_gambes - 30 <= 0 ? 0 :  $team->poin_gambes - 30;
                        }
                        $team->save();

                        // Update num occupants pada territory
                        $territory->num_occupants = $territory->num_occupants + 1;
                        $territory->save();

                        $status = 'success';
                        $response = 'success';
                        $msg = 'Berhasil melakukan spawn pada pelabuhan yang dipilih';
                    }
                    // Territory yang dipilih bukan daerah spawn
                    else {
                        $status = 'error';
                        $response = 'error';
                        $msg = 'Territory yang dipilih bukan daerah spawn!';
                    }
                }
                // Territory yang dipilih tidak valid
                else {
                    $status = 'error';
                    $response = 'error';
                    $msg = 'Territory tidak valid!';
                }
            }
            // team sudah berada pada suatu territory
            else {
                $status = 'error';
                $response = 'error';
                $msg = 'team sudah berada pada suatu territory!';
            }
        }
        // Penpos belum select team
        else {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap select team terlebih dahulu!';
        }

        if($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function move(){
    }

    public function action(){
    }
}
