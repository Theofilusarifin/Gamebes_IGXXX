<?php

namespace App\Http\Controllers\Penpos;

use App\Http\Controllers\Controller;
use App\Territory;
use App\SeasonNow;
use App\Team;
use App\Events\UpdateMap;
use App\IngridientStore;
use App\MachineStore;
use App\Service;
use App\Transport;
use App\TransportStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MapController extends Controller
{
    public function index()
    {
        $territories = Territory::all();
        $teams = Team::all();
        return view('penpos.map.index', compact('territories', 'teams'));
    }

    public function updateMap()
    {
        $territories = Territory::with('teams')->get();
        return response()->json(array(
            'territories' => $territories,
        ), 200);
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
                        // Update territory team ke territory Pelabuhan
                        $team->territory_id = $territory->id;
                        // Kalau panas hujan harganya 30
                        $cost = 30;
                        // Kalau salju berkurang 60
                        if ($season_now->number = 3){
                            $cost = 60;
                        }

                        if ($team->tc - $cost >= 0){
                            // Kurangi tc sesuai harga spawn
                            $team->tc = $team->tc - $cost;
                            // Tambah spend sesuai harga spawn
                            $team->tc = $team->total_spend + $cost;
                        }
                        else{
                            $team->tc = 0;
                        }

                        $team->total_spawn = $team->total_spawn + 1;

                        $team->save();

                        // Update num occupants pada territory
                        $territory->num_occupant = $territory->num_occupant + 1;
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

    public function move(Request $request){
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $arah = $request['arah'];
        $lebar_map = 38;

        // Status dan message yang diberikan
        $status = '';
        $msg = '';
        $response = 'success';

        // Kalau penpos sudah select team
        if (isset($team)) {
            // Kalau dia sudah di map  
            if (isset($team->territory_id)) {
                // Pengecekan apakah capacity sudah melebihi
                // $sisa_move = $request['sisa_move'];
                if (false) {
                    // $response = 'error';
                    // $status = 'error';
                    // $msg = 'Sisa langkah telah habis!';
                } else {
                    // Posisi lama pemain
                    $t_id = $team->territory_id;
                    if ($t_id < 260) $lebar_map = 51;
                    else if ($t_id < 362) $lebar_map = 50;
                    else if ($t_id < 463) $lebar_map = 51;
                    else if ($t_id < 564) $lebar_map = 50;
                    else if ($t_id < 665) $lebar_map = 51;
                    else if ($t_id < 766) $lebar_map = 50;
                    else if ($t_id < 867) $lebar_map = 51;
                    else if ($t_id < 968) $lebar_map = 50;
                    else if ($t_id < 1069) $lebar_map = 51;

                    // Territory Lama
                    $old_territory = Territory::find($t_id);

                    // Menentukan Posisi
                    if ($arah == 'atas') $t_id -= $lebar_map;
                    else if ($arah == 'kanan_atas') $t_id -= ($lebar_map + 1);
                    else if ($arah == 'kiri_atas')  $t_id -= ($lebar_map - 1);
                    else if ($arah == 'kanan') $t_id += 1;
                    else if ($arah == 'kiri') $t_id -= 1;
                    else if ($arah == 'bawah') $t_id += $lebar_map;
                    else if ($arah == 'kanan_bawah') $t_id += ($lebar_map + 1);
                    else if ($arah == 'kiri_bawah') $t_id += ($lebar_map - 1);

                    // Ambil territory
                    $new_territory = Territory::find($t_id);
                    // Territory baru adalah territory valid
                    if (isset($new_territory)) {
                        // Pengecekan Posisi Apakah Ada 2 orang
                        if ($new_territory->num_occupant >= 2) {
                            $status = 'error';
                            $response = 'error';
                            $msg = 'Territory yang hendak anda tempati sudah ditempati dua orang!';
                        }
                        // Pengecekan apakah territory adalah wall?
                        else if ($new_territory->is_wall) {
                            $status = 'error';
                            $response = 'error';
                            $msg = 'Tidak dapat melewati wall!';
                        }
                        // Territory aman
                        else {
                            // Update Posisi ke database
                            $team->territory_id = $t_id;
                            $team->save();

                            // Update num occupant di territory lama
                            $old_territory->num_occupant = $old_territory->num_occupant - 1;
                            $old_territory->save();
                            // Update num occupant di territory baru
                            $new_territory->num_occupant = $new_territory->num_occupant + 1;
                            $new_territory->save();

                            $status = '';
                            $msg = '';
                        }
                    }
                    // Territory baru tidak valid
                    else {
                        $status = 'error';
                        $response = 'error';
                        $msg = 'Territory tidak valid!';
                    }
                }
            }
            // team belum select Pelabuhan
            else {
                $status = 'error';
                $response = 'error';
                $msg = 'Harap pilih Pelabuhan terlebih dahulu!';
            }
        }
        // Penpos belum select team
        else {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap select team terlebih dahulu!';
        }

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function action(Request $request){
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);

        // Status dan message yang diberikan
        $msg = '';
        $status = 'error';
        $response = 'error';

        $store = null;
        $store_items = null;
        // Check Terrirory
        if ($territory->transport_store_id != null) {
            $store = TransportStore::find($territory->transport_store_id);
            $store_items = $store-> transports->all();
            $status = 'success';
            $response = 'success';
        }
        else if ($territory->ingridient_store_id != null){
            $store = IngridientStore::find($territory->ingridient_store_id);
            $store_items = $store->ingridients->all();
            $status = 'success';
            $response = 'success';
        }
        else if ($territory->machine_store_id != null){
            $store = MachineStore::find($territory->machine_store_id);
            $store_items = $store->machines->all();
            $status = 'success';
            $response = 'success';
        }
        else if ($territory->service_id != null) {
            $store = Service::find($territory->service_id);
            $status = 'success';
            $response = 'success';
        }

        if ($status == 'success') $msg = "Berhasil melakukan action pada store yang dipilih";
        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
            'store' => $store,
            'store_items' => $store_items,
        ), 200);
    }

    public function buy(Request $request){
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $banyak_item = $request['banyak_item'];
        // Status dan message yang diberikan
        $msg = '';
        $status = 'error';
        $response = 'error';

        // Check Terrirory
        if ($territory->transport_store_id != null) {
            if ($request['store_id'] == $territory->transport_store_id){
                $store = TransportStore::find($territory->transport_store_id);
                $data_stores = $store->transports->first();
                $stock = $data_stores->pivot->stock;
            }
            $status = 'success';
            $response = 'success';
        } else if ($territory->ingridient_store_id != null) {
            if ($request['store_id'] == $territory->ingridient_store_id) {
                $store = IngridientStore::find($territory->ingridient_store_id);
                $data_stores = $store->ingridients->first();
                $stock = $data_stores->pivot->stock;
            }
            $status = 'success';
            $response = 'success';
        } else if ($territory->machine_store_id != null) {
            if ($request['store_id'] == $territory->machine_store_id) {
                $store = MachineStore::find($territory->machine_store_id);
                $data_stores = $store->machines->first();
                $stock = $data_stores->pivot->stock;
            }
            $status = 'success';
            $response = 'success';
        } else if ($territory->service_id != null) {
            $store = Service::find($territory->service_id);
            $status = 'success';
            $response = 'success';
        }

        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
            'store' => $store,
        ),
            200
        );
    }
}
