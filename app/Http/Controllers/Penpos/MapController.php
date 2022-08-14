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
use App\TeamMachine;
use App\TeamTransport;
use App\Transport;
use App\TransportStore;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        $territories = Territory::where('id', '<=', 840)->get();
        $upper_companies = Territory::where('id', '>=', 1000)->where('id', '<=', 1041)->get();
        $right_companies = Territory::where('id', '>=', 1042)->where('id', '<=', 1061)->get();

        // Desencending karena arahnya berlawanan dengan codingan TR dan TD
        $lower_companies = Territory::where('id', '>=', 1062)->where('id', '<=', 1103)->orderBy('id', 'DESC')->get();
        $left_companies = Territory::where('id', '>=', 1104)->where('id', '<=', 1123)->orderBy('id', 'DESC')->get();

        // $teamCoba = Team::find(1);
        // dd($teamCoba);
        $teams = Team::all();
        return view('penpos.map.index', compact('territories', 'teams', 'upper_companies', 'right_companies', 'lower_companies', 'left_companies'));
    }

    public function updateMap()
    {
        $territories = Territory::with('teams')->where('id', '<=', 840)->get();

        $upper_companies = Territory::with('teams')->where('id', '>=', 1000)->where('id', '<=', 1041)->get();
        $right_companies = Territory::with('teams')->where('id', '>=', 1042)->where('id', '<=', 1061)->get();

        // Desencending karena arahnya berlawanan dengan codingan TR dan TD
        $lower_companies = Territory::with('teams')->where('id', '>=', 1062)->where('id', '<=', 1103)->orderBy('id', 'DESC')->get();
        $left_companies = Territory::with('teams')->where('id', '>=', 1104)->where('id', '<=', 1123)->orderBy('id', 'DESC')->get();

        return response()->json(array(
            'territories' => $territories,
            'upper_companies' => $upper_companies,
            'lower_companies' => $lower_companies,
            'right_companies' => $right_companies,
            'left_companies' => $left_companies,
        ), 200);
    }

    public function spawn(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($request['territory_id']);
        $season_now = SeasonNow::first();

        // Status dan message yang diberikan
        $response = '';
        $status = '';
        $msg = '';

        // Kalau penpos belum select team
        if (!isset($team)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap select team terlebih dahulu!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // team sudah berada pada suatu territory
        if ((isset($team->territory_id)) && ($team->territory_id < 1000)) {
            $status = 'error';
            $response = 'error';
            $msg = 'team sudah berada pada suatu territory!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Territory yang dipilih tidak valid
        if (!isset($territory)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory tidak valid!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Territory yang dipilih bukan daerah spawn
        if (!$territory->is_harbour) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory yang dipilih bukan daerah spawn!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kalau lagi di company, hilangkan pion di company 
        if ($team->territory_id > 1000) {
            $team_company = Territory::find($team->territory_id);
            $team_company->num_occupant = $team_company->num_occupant - 1;
            $team_company->save();
        }

        // Update territory team ke territory Pelabuhan
        $team->territory_id = $territory->id;

        // Kalau musim panas/hujan harga spawn adalah 30
        $cost = 30;
        // Kalau musim salju harga spawn adalah 60
        if ($season_now->number == 3) {
            $cost = 60;
        }

        // Apabila TC yang dimiliki lebih besar dari harga
        if ($team->tc - $cost >= 0) {
            // Kurangi tc sesuai harga spawn
            $team->tc = $team->tc - $cost;
            // Tambah spend sesuai harga spawn
            $team->total_spend = $team->total_spend + $cost;
        } else {
            $team->tc = 0;
        }

        // Tambahkan total spawn
        $team->total_spawn = $team->total_spawn + 1;
        $team->save();

        // Update num occupants pada territory
        $territory->num_occupant = $territory->num_occupant + 1;
        $territory->save();

        $status = 'success';
        $response = 'success';
        $msg = 'Berhasil melakukan spawn pada pelabuhan yang dipilih';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function move(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $arah = $request['arah'];
        $jalan_lurus = false;
        $jalan_diagonal = false;
        $harga_jalan = 0;

        // Status dan message yang diberikan
        $status = '';
        $msg = '';
        $response = 'success';

        // Penpos belum select team
        if (!isset($team)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap select team terlebih dahulu!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila team belum berada pada suatu territory atau berada di home
        if ((!isset($team->territory_id)) || ($team->territory_id > 1000)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap pilih Pelabuhan terlebih dahulu!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Posisi lama pemain
        $t_id = $team->territory_id;
        // Territory Lama
        $old_territory = Territory::find($t_id);

        // Menentukan lebar map
        $lebar_map = 42;

        // Menentukan Posisi
        if ($arah == 'atas') {
            $t_id -= $lebar_map;
            $jalan_lurus = true;
        } else if ($arah == 'kanan_atas') {
            $t_id -= ($lebar_map + 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kiri_atas') {
            $t_id -= ($lebar_map - 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kanan') {
            $t_id += 1;
            $jalan_lurus = true;
        } else if ($arah == 'kiri') {
            $t_id -= 1;
            $jalan_lurus = true;
        } else if ($arah == 'bawah') {
            $t_id += $lebar_map;
            $jalan_lurus = true;
        } else if ($arah == 'kanan_bawah') {
            $t_id += ($lebar_map + 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kiri_bawah') {
            $t_id += ($lebar_map - 1);
            $jalan_diagonal = true;
        }

        // Menentukan harga jalan
        if ($jalan_lurus) $harga_jalan = 1;
        else if ($jalan_diagonal) $harga_jalan = 3;

        // Apabila uang dari team tidak cukup untuk harga jalan
        if ($team->tc < $harga_jalan) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tiggie Coin anda tidak cukup untuk melakukan perjalanan!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Update uang team
        $team->tc = $team->tc - $harga_jalan;
        // Update Total Spen Team
        $team->total_spend = $team->total_spend + $harga_jalan;

        // Update banyak jalan team
        if ($jalan_lurus) {
            $team->s_moves = $team->s_moves + 1;
        } else if ($jalan_diagonal) {
            $team->d_moves = $team->d_moves + 1;
        }

        // Ambil territory
        $new_territory = Territory::find($t_id);

        // Territory baru tidak valid
        if (!isset($new_territory)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory tidak valid!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }


        // Pengecekan Posisi Apakah Ada 2 orang
        else if ($new_territory->num_occupant >= 2) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory sudah ditempati dua orang!';
        }
        // Pengecekan apakah territory adalah wall?
        else if ($new_territory->is_wall) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tidak dapat melewati wall!';
        }
        // Territory aman
        else {
            $status = '';
            $msg = '';

            // Pengecekan apakah ada 1 orang
            if ($new_territory->num_occupant == 1) {
                $status = 'error';
                $response = 'success';
                $msg = 'TC berkurang karena menabrak tim lain!';

                // Kurangi TC team yang menabrak 
                $team->tc = $team->tc - 20;
                $team->total_spend = $team->total_spend + 20;
                $team->total_crash = $team->total_crash + 1;

                // Tambahkan TC team yang ditabrak
                $crashed_team = Team::where('territory_id', $new_territory->id)->first();
                $crashed_team->tc = $crashed_team->tc + 20;
                $crashed_team->total_income = $crashed_team->total_income + 20;
                $crashed_team->save();
            }

            // Update Posisi ke database
            $team->territory_id = $t_id;
            $team->save();

            // Update num occupant di territory lama
            $old_territory->num_occupant = $old_territory->num_occupant - 1;
            $old_territory->save();
            // Update num occupant di territory baru
            $new_territory->num_occupant = $new_territory->num_occupant + 1;
            $new_territory->save();
        }

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function undo(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $arah = $request['arah'];
        $jalan_lurus = false;
        $jalan_diagonal = false;
        $harga_jalan = 0;

        // Status dan message yang diberikan
        $status = '';
        $msg = '';
        $response = 'success';

        // Penpos belum select team
        if (!isset($team)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap select team terlebih dahulu!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila team belum berada pada suatu territory atau berada di home
        if ((!isset($team->territory_id)) || ($team->territory_id > 1000)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Harap pilih Pelabuhan terlebih dahulu!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Posisi lama pemain
        $t_id = $team->territory_id;
        // Territory Lama
        $old_territory = Territory::find($t_id);

        // Menentukan lebar map
        $lebar_map = 42;

        // Menentukan Posisi
        if ($arah == 'atas') {
            $t_id -= $lebar_map;
            $jalan_lurus = true;
        } else if ($arah == 'kanan_atas') {
            $t_id -= ($lebar_map + 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kiri_atas') {
            $t_id -= ($lebar_map - 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kanan') {
            $t_id += 1;
            $jalan_lurus = true;
        } else if ($arah == 'kiri') {
            $t_id -= 1;
            $jalan_lurus = true;
        } else if ($arah == 'bawah') {
            $t_id += $lebar_map;
            $jalan_lurus = true;
        } else if ($arah == 'kanan_bawah') {
            $t_id += ($lebar_map + 1);
            $jalan_diagonal = true;
        } else if ($arah == 'kiri_bawah') {
            $t_id += ($lebar_map - 1);
            $jalan_diagonal = true;
        }

        // Menentukan harga jalan
        if ($jalan_lurus) $harga_jalan = 1;
        else if ($jalan_diagonal) $harga_jalan = 3;

        // Update uang team
        $team->tc = $team->tc + $harga_jalan;
        // Update Total Spen Team
        $team->total_spend = $team->total_spend - $harga_jalan;

        // Update banyak jalan team
        if ($jalan_lurus) {
            $team->s_moves = $team->s_moves - 1;
        } else if ($jalan_diagonal) {
            $team->d_moves = $team->d_moves - 1;
        }

        // Ambil territory
        $new_territory = Territory::find($t_id);

        // Territory baru tidak valid
        if (!isset($new_territory)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory tidak valid!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Pengecekan Posisi Apakah Ada 2 orang
        else if ($new_territory->num_occupant >= 2) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory sudah ditempati dua orang!';
        }
        // Pengecekan apakah territory adalah wall?
        else if ($new_territory->is_wall) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tidak dapat melewati wall!';
        }
        // Territory aman
        else {
            $status = '';
            $msg = '';

            // Pengecekan apakah ada 1 orang
            if ($new_territory->num_occupant == 1) {
                $status = 'error';
                $response = 'success';
                $msg = 'TC berkurang karena menabrak tim lain!';

                // Kurangi TC team yang menabrak 
                $team->tc = $team->tc - 20;
                $team->total_spend = $team->total_spend + 20;
                $team->total_crash = $team->total_crash + 1;

                // Tambahkan TC team yang ditabrak
                $crashed_team = Team::where('territory_id', $new_territory->id)->first();
                $crashed_team->tc = $crashed_team->tc + 20;
                $crashed_team->total_income = $crashed_team->total_income + 20;
                $crashed_team->save();
            }

            // Update Posisi ke database
            $team->territory_id = $t_id;
            $team->save();

            // Update num occupant di territory lama
            $old_territory->num_occupant = $old_territory->num_occupant - 1;
            $old_territory->save();
            // Update num occupant di territory baru
            $new_territory->num_occupant = $new_territory->num_occupant + 1;
            $new_territory->save();
        }

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function getCapacity(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $status = 'success';
        return response()->json(array(
            'team' => $team,
            'status' => $status,
        ), 200);
    }

    public function checkCapacity(Team $team, $item_weight)
    {
        // item_weight itu jumlah * berat per ingridient/mesin
        // Ambil kendaraan paling besar kapasitasnya
        $team_transport = $team->transports()->orderByDesc('id')->first();
        // Pengecekan apakah capacity sudah melebihi

        if ($team->current_capacity + $item_weight > $team_transport->max_weight) {
            return false;
        }
        return true;
    }

    public function action(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);

        // Status dan message yang diberikan
        $response = 'error';
        $status = 'error';
        $msg = 'Daerah yang dipilih bukan merupakan sebuah store!';
        $type = '';

        $store = null;
        $store_items = null;

        // Check Terrirory
        if ($territory->transport_store_id != null) {
            // inisialisasi store
            $store = TransportStore::find($territory->transport_store_id);
            $store_items = $store->transports->all();
            $status = 'success';
            $response = 'success';
            $type = 'Transport';
        } else if ($territory->ingridient_store_id != null) {
            // inisialisasi store
            $store = IngridientStore::find($territory->ingridient_store_id);
            $store_items = $store->ingridients->all();
            $status = 'success';
            $response = 'success';
            $type = 'Ingridient';
        } else if ($territory->machine_store_id != null) {
            // inisialisasi store
            $store = MachineStore::find($territory->machine_store_id);
            $store_items = $store->machines->all();
            $status = 'success';
            $response = 'success';
            $type = 'Machine';
        } else if ($territory->service_id != null) {
            // inisialisasi store
            $store = Service::find($territory->service_id);
            $status = 'success';
            $response = 'success';
            $type = 'Service';
        }

        if ($status == 'success') $msg = "Berhasil melakukan action pada store yang dipilih";
        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
            'type' => $type,
            'store' => $store,
            'store_items' => $store_items,
        ), 200);
    }

    public function buyTransport(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $item_id = $request['item_id'];
        $banyak_item = $request['banyak_item'];
        $biaya = 0;

        // Status dan message yang diberikan
        $msg = '';
        $status = '';
        $response = '';

        if ($territory->transport_store_id == null) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory bukan merupakan transport store!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Ambil Store
        $store = TransportStore::find($territory->transport_store_id);
        // Ambil Item di store sesuai dengan yang dipilih
        $item = $store->transports->where('id', $item_id)->first();
        // Ambil biaya
        $biaya = $item->price;
        // Ambil data item dari team
        $team_item = $team->transports->where('id', $item_id)->first();

        // Ambil stock dari store
        $stock = $item->pivot->stock;
        // Apabila stock tidak tersedia
        if ($stock < $banyak_item) {
            $status = 'error';
            $response = 'error';
            $msg = 'Stock tidak tersedia!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $biaya_total = $biaya * $banyak_item;

        // Apabila TC yang dimiliki tidak cukup
        if ($team->tc < $biaya_total) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tiggie Coin yang anda miliki tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kurangi TC dari team untuk pembayaran
        $team->tc = $team->tc - $biaya_total;
        // Tambahkan team total spend 
        $team->total_spend = $team->total_spend + $biaya_total;
        $team->save();

        // Update Stock
        $item->transportStores()->sync([$store->id => ['stock' => $item->pivot->stock - $banyak_item]], false);

        for ($i = 0; $i < $banyak_item; $i++) {
            // Tambahkan data transport baru
            $teamTransport = new TeamTransport;
            $teamTransport->team_id = $team->id;
            $teamTransport->transport_id = Transport::find($item_id)->id;

            // Simpan Data baru
            $teamTransport->save();
        }
        // // Update tambahkan banyak yang sekarang dengan yang dibeli
        // $team->transports()->sync([$item->id => ['amount_have' => $amount_have + $banyak_item]], false);

        $status = 'success';
        $response = 'success';
        $msg = 'Pembelian berhasil';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function buyIngridient(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $item_id = $request['item_id'];
        $banyak_item = $request['banyak_item'];
        $biaya = 0;

        // Status dan message yang diberikan
        $msg = '';
        $status = '';
        $response = '';

        if ($territory->ingridient_store_id == null) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory bukan merupakan ingridient store!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Ambil Store
        $store = IngridientStore::find($territory->ingridient_store_id);
        // Ambil Item di store sesuai dengan yang dipilih
        $item = $store->ingridients->where('id', $item_id)->first();
        // Ambil biaya dari season sekarang
        $biaya = $item->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;

        $total_weight = $item->weight * $banyak_item;

        // MSG dan Garam harus beli 3 bungkus
        // Garam id 6, MSG id 8
        if ($item_id == 6 || $item_id == 8) {
            if ($banyak_item % 3 != 0) { {
                    $status = 'error';
                    $response = 'error';
                    $msg = 'Pembelian ingridient ' . $item->name . ' harus berkelipatan 3!';

                    return response()->json(array(
                        'response' => $response,
                        'status' => $status,
                        'msg' => $msg,
                    ), 200);
                }
            }
            // Hitung berat total ingridient yang mau dibeli
            $total_weight = $item->weight * ($banyak_item / 3);
        }

        // Ambil stock dari store
        $stock = $item->pivot->stock;
        // Apabila Stock tidak tersedia
        if ($stock < $banyak_item) {
            $status = 'error';
            $response = 'error';
            $msg = 'Stock tidak tersedia!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Check Capacity
        if (!$this->checkCapacity($team, $total_weight)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Kapasitas kendaraan tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $biaya_total = $biaya * $banyak_item;
        // Apanbila tc yang dimiliki tidak cukup
        if ($team->tc < $biaya_total) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tiggie Coin yang anda miliki tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kurang TC dari team untuk pembayaran
        $team->tc = $team->tc - $biaya_total;
        // Tambahkan team total spend 
        $team->total_spend = $team->total_spend + $biaya_total;

        $item->ingridientStores()->sync([$store->id => ['stock' => $item->pivot->stock - $banyak_item]], false);

        // Waktu di Surabaya sekarang
        $start = DB::select(DB::raw("SELECT CURRENT_TIMESTAMP() as waktu"))[0]->waktu;
        // Tambah 10 menit waktu di surabaya sekarang
        $expired_time = date('Y-m-d H:i:s', strtotime('+20 minutes', strtotime($start)));

        // Update tambahkan ingridient yang dibeli
        $team->ingridients()->attach($item->id, [
            'amount_have' => $banyak_item,
            'expired_time' => $expired_time,
            'total' => $biaya_total
        ]);

        // Tambahkan capacity team
        $team->current_capacity = $team->current_capacity + $total_weight;
        $team->save();

        $status = 'success';
        $response = 'success';
        $msg = 'Pembelian berhasil';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function buyMachine(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $item_id = $request['item_id'];
        $banyak_item = $request['banyak_item'];
        $biaya = 0;

        // Status dan message yang diberikan
        $msg = '';
        $status = '';
        $response = '';

        if ($territory->machine_store_id == null) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory bukan merupakan machine store!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Ambil Store
        $store = MachineStore::find($territory->machine_store_id);
        // Ambil Item di store sesuai dengan yang dipilih
        $item = $store->machines->where('id', $item_id)->first();
        // Ambil biaya
        $biaya = $item->price;
        // Ambil stock dari store
        $stock = $item->pivot->stock;

        // Hitung total weight
        $total_weight = $item->weight * $banyak_item;

        // Check Capacity
        if (!$this->checkCapacity($team, $total_weight)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Kapasitas kendaraan tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        if ($stock < $banyak_item) {
            $status = 'error';
            $response = 'error';
            $msg = 'Stock tidak tersedia!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $biaya_total = $biaya * $banyak_item;
        // Apabila tc team tidak cukup
        if ($team->tc < $biaya_total) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tiggie Coin yang anda miliki tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kurangi TC dari team untuk pembayaran
        $team->tc = $team->tc - $biaya_total;
        // Tambahkan team total spend 
        $team->total_spend = $team->total_spend + $biaya_total;
        // Tambahkan current kapasitas dengan weight item yang dibeli
        $team->current_capacity = $team->current_capacity + $total_weight;

        $team->save();

        // Kurangi stock machine store
        $item->machineStores()->sync([$store->id => ['stock' => $item->pivot->stock - $banyak_item]], false);

        for ($i = 0; $i < $banyak_item; $i++) {
            // Tambahkan data baru
            $team_machine = new TeamMachine;
            $team_machine->selected = 0;
            $team_machine->is_used = 0;
            $team_machine->product_produced = 0;
            $team_machine->performance = 100;
            $team_machine->season_buy = SeasonNow::first()->number;
            $team_machine->team_id = $team->id;
            $team_machine->machine_id = $item->id;
            // Simpan Data Baru
            $team_machine->save();
        }

        $status = 'success';
        $response = 'success';
        $msg = 'Pembelian berhasil';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function buyService(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $item_id = $request['item_id'];
        $banyak_item = $request['banyak_item'];
        $biaya = 0;

        // Status dan message yang diberikan
        $msg = '';
        $status = '';
        $response = '';

        // Check Apakah territory merupaka Service
        if ($territory->service_id == null) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory bukan merupakan service store!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Inisiasi service store
        $store = Service::find($territory->service_id);
        // Harga service
        $biaya = $store->price;
        // Ambil stock dari store
        $stock = $store->stock;

        // Apabila stock tidak tersedia
        if ($stock < $banyak_item) {
            // Stock tidak tersedia
            $status = 'error';
            $response = 'error';
            $msg = 'Stock tidak tersedia!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila TC yang dimiliki tidak cukup
        if ($team->tc < $biaya) {
            $status = 'error';
            $response = 'error';
            $msg = 'Tiggie Coin yang anda miliki tidak cukup!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kurangi TC dari team untuk pembayaran
        $team->tc = $team->tc - $biaya;
        // Tambahkan team total spend 
        $team->total_spend = $team->total_spend + $biaya;
        // Kurangi stock
        $store->stock = $store->stock - $banyak_item;
        // Simpan perubahan
        $store->save();
        // Team memiliki jasa service
        $team->service_id = $store->id;
        // Simpan data team
        $team->save();

        $status = 'success';
        $response = 'success';
        $msg = 'Pembelian berhasil';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function checkCompany($team_id)
    {
        $territory_id = 0;
        if ($team_id <= 10) {
            $territory_id = 1003 + (4 * ($team_id - 1));
        } else if ($team_id <= 15) {
            $territory_id = 1042 + (4 * ($team_id - 11));
        } else if ($team_id <= 25) {
            $territory_id = 1100 - (4 * (25 - $team_id));
        } else if ($team_id <= 30) {
            $territory_id = 1123 + (4 * (30 - $team_id));
        }

        return $territory_id;
    }

    public function backToCompany(Request $request)
    {
        // Status dan message yang diberikan
        $status = '';
        $msg = '';
        $response = 'success';

        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        // Territory Lama
        $old_territory = Territory::find($team->territory_id);

        // Check Company
        $territory_id = $this->checkCompany($team->id);

        // Companynya
        $team_company = Territory::find($territory_id);

        if ($team_company->id == $old_territory->id) {
            $status = 'error';
            $response = 'error';
            $msg = 'Team ini sudah berada pada company!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Territory baru adalah territory yang tidak valid
        if (!isset($team_company)) {
            $status = 'error';
            $response = 'error';
            $msg = 'Territory tidak valid!';

            return response()->json(array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Update Posisi ke database
        $team->territory_id = $team_company->id;
        $team->current_capacity = 0;
        $team->save();

        // Update num occupant di territory lama
        $old_territory->num_occupant = $old_territory->num_occupant - 1;
        $old_territory->save();
        // Update num occupant di territory baru
        $team_company->num_occupant = $team_company->num_occupant + 1;
        $team_company->save();

        $status = 'success';
        $msg = 'Berhasil kembali ke company';

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(array(
            'response' => $response,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
