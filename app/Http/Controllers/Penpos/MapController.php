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
use App\Transport;
use App\TransportStore;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
                        if ($season_now->number = 3) {
                            $cost = 60;
                        }

                        if ($team->tc - $cost >= 0) {
                            // Kurangi tc sesuai harga spawn
                            $team->tc = $team->tc - $cost;
                            // Tambah spend sesuai harga spawn
                            $team->tc = $team->total_spend + $cost;
                        } else {
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
        $lebar_atas = 0;
        $lebar_bawah = 0;
        $jalan_lurus = false;
        $jalan_diagonal = false;
        $harga_jalan = 0;

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
                    // Territory Lama
                    $old_territory = Territory::find($t_id);

                    // Menentukan lebar map
                    if ($t_id < 210) {
                        $lebar_atas = 50;
                        $lebar_bawah = 51;
                    } else if ($t_id < 261) {
                        $lebar_atas = 51;
                        $lebar_bawah = 51;
                    } else if ($t_id < 312) {
                        $lebar_atas = 51;
                        $lebar_bawah = 50;
                    } else if ($t_id < 362) {
                        $lebar_atas = 50;
                        $lebar_bawah = 50;
                    } else if ($t_id < 412) {
                        $lebar_atas = 50;
                        $lebar_bawah = 51;
                    } else if ($t_id < 463) {
                        $lebar_atas = 51;
                        $lebar_bawah = 51;
                    } else if ($t_id < 514) {
                        $lebar_atas = 51;
                        $lebar_bawah = 50;
                    } else if ($t_id < 564) {
                        $lebar_atas = 50;
                        $lebar_bawah = 50;
                    } else if ($t_id < 615) {
                        $lebar_atas = 50;
                        $lebar_bawah = 51;
                    } else if ($t_id < 665) {
                        $lebar_atas = 51;
                        $lebar_bawah = 51;
                    } else if ($t_id < 716) {
                        $lebar_atas = 51;
                        $lebar_bawah = 50;
                    } else if ($t_id < 766) {
                        $lebar_atas = 50;
                        $lebar_bawah = 50;
                    } else if ($t_id < 816) {
                        $lebar_atas = 50;
                        $lebar_bawah = 51;
                    } else if ($t_id < 867) {
                        $lebar_atas = 51;
                        $lebar_bawah = 51;
                    } else if ($t_id < 918) {
                        $lebar_atas = 51;
                        $lebar_bawah = 50;
                    } else if ($t_id < 968) {
                        $lebar_atas = 50;
                        $lebar_bawah = 50;
                    } else if ($t_id < 1018) {
                        $lebar_atas = 50;
                        $lebar_bawah = 51;
                    } else if ($t_id < 1069) {
                        $lebar_atas = 51;
                        $lebar_bawah = 51;
                    }

                    // Menentukan Posisi
                    if ($arah == 'atas') {
                        $t_id -= $lebar_atas;
                        $jalan_lurus = true;
                    } else if ($arah == 'kanan_atas') {
                        $t_id -= ($lebar_atas + 1);
                        $jalan_diagonal = true;
                    } else if ($arah == 'kiri_atas') {
                        $t_id -= ($lebar_atas - 1);
                        $jalan_diagonal = true;
                    } else if ($arah == 'kanan') {
                        $t_id += 1;
                        $jalan_lurus = true;
                    } else if ($arah == 'kiri') {
                        $t_id -= 1;
                        $jalan_lurus = true;
                    } else if ($arah == 'bawah') {
                        $t_id += $lebar_bawah;
                        $jalan_lurus = true;
                    } else if ($arah == 'kanan_bawah') {
                        $t_id += ($lebar_bawah + 1);
                        $jalan_diagonal = true;
                    } else if ($arah == 'kiri_bawah') {
                        $t_id += ($lebar_bawah - 1);
                        $jalan_diagonal = true;
                    }

                    if ($jalan_lurus) $harga_jalan = 1;
                    else if ($jalan_diagonal) $harga_jalan = 3;

                    if ($team->tc >= $harga_jalan) {
                        $team->tc = $team->tc - $harga_jalan;
                        if ($jalan_lurus) {
                            $team->s_moves = $team->s_moves + 1;
                        } else if ($jalan_diagonal) {
                            $team->d_moves = $team->d_moves + 1;
                        }

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
                    } else {
                        $status = 'error';
                        $response = 'error';
                        $msg = 'Tiggie Coin anda tidak cukup untuk melakukan perjalanan!';
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

    public function action(Request $request)
    {
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
            // inisialisasi store
            $store = TransportStore::find($territory->transport_store_id);
            $store_items = $store->transports->all();
            $status = 'success';
            $response = 'success';
        } else if ($territory->ingridient_store_id != null) {
            // inisialisasi store
            $store = IngridientStore::find($territory->ingridient_store_id);
            $store_items = $store->ingridients->all();
            $status = 'success';
            $response = 'success';
        } else if ($territory->machine_store_id != null) {
            // inisialisasi store
            $store = MachineStore::find($territory->machine_store_id);
            $store_items = $store->machines->all();
            $status = 'success';
            $response = 'success';
        } else if ($territory->service_id != null) {
            // inisialisasi store
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

    public function buy(Request $request)
    {
        // Ambil variabel awal yang dibutuhkan
        $team = Team::find($request['team_id']);
        $territory = Territory::find($team->territory_id);
        $item_id = $request['item_id'];
        $banyak_item = $request['banyak_item'];
        $biaya = 0;

        // Status dan message yang diberikan
        $msg = '';
        $status = 'error';
        $response = 'error';

        // Check Apakah Service
        if ($territory->service_id != null) {
            $store = Service::find($territory->service_id);
            $biaya = $store->price;

            // Ambil stock dari store
            $stock = $store->stock;
            // Check apakah stock tersedia
            if ($stock >= $banyak_item) {
                // Kalau uangnya cukup
                if ($team->tc >= $biaya) {
                    // Kurang TC dari team untuk pembayaran
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
                }
                // TC ga cukup untuk beli
                else {
                    $status = 'error';
                    $response = 'error';
                    $msg = 'Tiggie Coin yang anda miliki tidak cukup!';
                }
            } else {
                // Stock tidak tersedia
                $status = 'error';
                $response = 'error';
                $msg = 'Stock tidak tersedia!';
            }
        }
        // Bukan Service
        else {
            // Apakah Machine Store ?
            if ($territory->machine_store_id != null) {
                // Check apakah store id yang dikirim sama dengan yang sedang di tempati
                if ($request['store_id'] == $territory->machine_store_id) {
                    // Ambil Store
                    $store = MachineStore::find($territory->machine_store_id);
                    // Ambil Item di store sesuai dengan yang dipilih
                    $item = $store->machines->where('id', $item_id)->first();
                    // Ambil biaya
                    $biaya = $item->price;
                }
                $status = 'success';
                $response = 'success';

                // Ambil stock dari store
                $stock = $item->pivot->stock;
                // Check apakah stock tersedia
                if ($stock >= $banyak_item) {
                    $biaya_total = $biaya * $banyak_item;
                    // Kalau uangnya cukup
                    if ($team->tc >= $biaya_total) {
                        // Kurang TC dari team untuk pembayaran
                        $team->tc = $team->tc - $biaya_total;
                        // Tambahkan team total spend 
                        $team->total_spend = $team->total_spend + $biaya_total;

                        $item->pivot->stock = $item->pivot->stock - $banyak_item;
                        // Simpan perubahan
                        $item->save();

                        // Tambahkan data baru
                        $team_machine = new TeamMachine;
                        $team_machine->performance = 100;
                        $team_machine->season_buy = SeasonNow::first()->number;
                        $team_machine->team_id = $team->id;
                        $team_machine->machine_id = $item->id;
                        // Simpan Data Baru
                        $team_machine->save();

                        $status = 'success';
                        $response = 'success';
                        $msg = 'Pembelian berhasil';
                    }
                    // TC ga cukup untuk beli
                    else {
                        $status = 'error';
                        $response = 'error';
                        $msg = 'Tiggie Coin yang anda miliki tidak cukup!';
                    }
                } else {
                    // Stock tidak tersedia
                    $status = 'error';
                    $response = 'error';
                    $msg = 'Stock tidak tersedia!';
                }
            } else {
                // Transport Store
                if ($territory->transport_store_id != null) {
                    // Check apakah store id yang dikirim sama dengan yang sedang di tempati
                    if ($request['store_id'] == $territory->transport_store_id) {
                        // Ambil Store
                        $store = TransportStore::find($territory->transport_store_id);
                        // Ambil Item di store sesuai dengan yang dipilih
                        $item = $store->transports->where('id', $item_id)->first();
                        // Ambil biaya
                        $biaya = $item->price;
                        // Ambil data item dari team
                        $team_item = $team->transports->where('id', $item_id)->first();

                        $amount_have = 0;
                        // Kalau punya barang ganti amount have sesuai yang dipunyai
                        if ($team_item != null) {
                            // Ambil banyaknya yang dimiliki sekarang
                            $amount_have = $team_item->pivot->amount_have;
                        }
                    }
                    $status = 'success';
                    $response = 'success';
                }
                // Ingridient Store
                else if ($territory->ingridient_store_id != null) {
                    // Check apakah store id yang dikirim sama dengan yang sedang di tempati
                    if ($request['store_id'] == $territory->ingridient_store_id) {
                        // Ambil Store
                        $store = IngridientStore::find($territory->ingridient_store_id);
                        // Ambil Item di store sesuai dengan yang dipilih
                        $item = $store->ingridients->where('id', $item_id)->first();
                        // Ambil biaya dari season sekarang
                        $biaya = $item->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;
                        // Ambil data item dari team
                        $team_item = $team->ingridients->where('id', $item_id)->first();

                        $amount_have = 0;
                        // Kalau punya barang ganti amount have sesuai yang dipunyai
                        if ($team_item != null) {
                            // Ambil banyaknya yang dimiliki sekarang
                            $amount_have = $team_item->pivot->amount_have;
                        }
                    }
                    $status = 'success';
                    $response = 'success';
                }

                // Ambil stock dari store
                $stock = $item->pivot->stock;
                // Check apakah stock tersedia
                if ($stock >= $banyak_item) {
                    $biaya_total = $biaya * $banyak_item;
                    // Kalau uangnya cukup
                    if ($team->tc >= $biaya_total) {
                        // Kurang TC dari team untuk pembayaran
                        $team->tc = $team->tc - $biaya_total;
                        // Tambahkan team total spend 
                        $team->total_spend = $team->total_spend + $biaya_total;

                        $item->pivot->stock = $item->pivot->stock - $banyak_item;
                        $item->save();

                        // Update tambahkan banyak yang sekarang dengan yang dibeli
                        $team->ingridients()->sync([$item->id => ['amount_have' => $amount_have + $banyak_item]], false);
                        $team->save();

                        $status = 'success';
                        $response = 'success';
                        $msg = 'Pembelian berhasil';
                    }
                    // TC ga cukup untuk beli
                    else {
                        $status = 'error';
                        $response = 'error';
                        $msg = 'Tiggie Coin yang anda miliki tidak cukup!';
                    }
                } else {
                    // Stock tidak tersedia
                    $status = 'error';
                    $response = 'error';
                    $msg = 'Stock tidak tersedia!';
                }
            }
        }

        if ($response != 'error') event(new UpdateMap("updateMap"));
        return response()->json(
            array(
                'response' => $response,
                'status' => $status,
                'msg' => $msg,
            ),
            200
        );
    }
}
