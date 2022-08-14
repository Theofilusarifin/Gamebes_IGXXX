<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Ingridient;
use App\Machine;
use App\MachineCombination;
use App\Product;
use App\Season;
use App\SeasonNow;
use App\Team;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProduksiController extends Controller
{
    public function game_authorization()
    {
        $season = Season::find(SeasonNow::first()->number);
        // Belum Mulai
        if ($season->number == 1 && $season->start_time == null && $season->end_time == null) {
            return false;
        }
        // Udah Selesai
        // Waktu di Surabaya sekarang
        $now = DB::select(DB::raw("SELECT CURRENT_TIMESTAMP() as waktu"))[0]->waktu;
        if ($season->end_time != null) {
            if ($season->number == 3 && $season->end_time < $now) {
                return false;
            }
        }
        return true;
    }

    public function index()
    {
        if (!$this->game_authorization()) {
            return redirect()->back();
        }

        return view('peserta.produksi.index');
    }

    public function updateIngridientUsed($team_id, $ingridient_id, $expired_time, $ingridient_left, $ingridient_used)
    {
        DB::table('ingridient_team')
            ->where('team_id', $team_id)
            ->where('ingridient_id', $ingridient_id)
            ->where('expired_time', $expired_time)
            ->update([
                'amount_have' => $ingridient_left,
                'amount_use' => $ingridient_used,
            ]);
    }

    public function usedAllIngridient($team_id, $ingridient_id, $expired_time, $ingridient_used)
    {
        DB::table('ingridient_team')
            ->where('team_id', $team_id)
            ->where('ingridient_id', $ingridient_id)
            ->where('expired_time', $expired_time)
            ->update([
                'amount_have' => 0,
                'amount_use' => $ingridient_used,
            ]);
    }

    public function getIngridient(Team $team, $ingridient_id)
    {
        return DB::table('ingridient_team')
            ->where('team_id', $team->id)
            ->where('ingridient_id', $ingridient_id)
            ->where('expired_time', '>=', date('Y-m-d H:i:s'))
            ->orderBy('expired_time', 'ASC')
            ->get();
    }

    public function sumIngridient(Team $team, $ingridient_id)
    {
        return $team->ingridients
            ->where('id', $ingridient_id)
            ->where('pivot.expired_time', '>=', date('Y-m-d H:i:s'))
            ->sortBy('pivot.expired_time')
            ->sum("pivot.amount_have");
    }

    public function deleteMachine($machine_id, $team_id)
    {
        DB::statement("DELETE FROM `team_machines` WHERE machine_id = " . $machine_id . " AND season_sell IS NULL AND team_id = " . $team_id);
    }

    public function production(Request $request)
    {
        // Ambil data dari ajax
        $team = Auth::user()->team;
        $product = Product::find($request['product_id']);
        
        // Tidak masukin Product
        if ($product == null) {
            $status = 'error';
            $msg = 'Pilih product terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Input user
        $banyak_item = $request['banyak_produksi']; //Kalau Kitosan kelipatan 1/2/3/4/5/6, sisanya kelipatan 4/8/12/16/20
        // Tidak masukin banyak_item
        if ($banyak_item == null) {
            $status = 'error';
            $msg = 'Masukkan banyak item terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        
        $tipe_udang = $request['tipe_udang']; //Id Udang yang dipilih team
        // Tidak masukin banyak_item
        if ($tipe_udang == null) {
            $status = 'error';
            $msg = 'Masukkan Tipe Udang terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Status dan message untuk respond
        $status = '';
        $msg = '';
        $ingridient_insufficient = false;
        //Ambil Season sekarang
        $season_now = SeasonNow::first()->number;
        //Variable message mesin dibuang ke sampah
        $trashMsg = '';

        // Team salah input angka ketika ingin produksi udang kaleng (1) atau saus udang (3)
        if (($product->id == 1 || $product->id == 3) && $banyak_item % 4 != 0) {
            $status = 'error';
            $msg = 'Jumlah produksi ' . $product->name . ' harus berkelipatan 4!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Produksi Udang Kaleng
        if ($product->id == 1) {
            // Hitung banyaknya produksi dengan cara dibagi 4
            $banyak_produksi = $banyak_item / 4;

            // Ambil team shrimp yang dimiliki dan tidak expired
            $total_shrimp = 0;
            $team_shrimps = $this->getIngridient($team, $tipe_udang);

            // Team tidak memiliki udang
            if ($team_shrimps == null) {
                $status = 'error';
                $msg = 'Team anda tidak memiliki ' . Ingridient::find($tipe_udang)->name . '!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Ambil team shrimp yang dimiliki dan tidak expired lalu dijumlahka semua
            $total_shrimp = $this->sumIngridient($team, $tipe_udang);

            // Ambil kombinasi machine udang kaleng yang digunakan oleh team saat ini
            $team_machine_combination = $team->machineCombinations
                ->where('id', '!=', '101')
                ->where('id', '!=', '102')
                ->first();

            // Team tidak memiliki kombinasi mesin yang sesuai
            if ($team_machine_combination == null) {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // CHECK PERFORMA MESIN
            //Ambil Season sekarang
            $season_now = SeasonNow::first()->number; // 1 panas, 2 hujan, 3 dingin
            //Ambil AC
            $mesin_ac = $team->teamMachines->where('machine_id', 4)->where('is_used', 1)->first();

            $kenaikan = 5; //Pengali untuk Penurunan performance
            if ($season_now == 1 && $mesin_ac == null) {
                $kenaikan = 10; //Pengali untuk penurunan performance
            }
            //Dapat data mesin-mesin (asli) yang digunakan sama tim pada kombinasinya kaleng udang

            $mesin_dalam_kombinasis = $team_machine_combination->machines;
            foreach ($mesin_dalam_kombinasis as $mesin_dalam_kombinasi) {
                //Ambil Team Machine yang idnya sama dengan $mesin_dalam_kombinasi dan is_usednya 1
                $timMesin = $team->teamMachines
                    ->where('machine_id', $mesin_dalam_kombinasi->id)
                    ->where('is_used', 1)
                    ->first();
                //Hitung kelipatan
                $kelipatan = floor(($banyak_item + $timMesin->product_produced) / 12);
                $totalPenurunanPerformance = $kelipatan * $kenaikan;
                //Cek Performance utk setiap mesin
                if ($timMesin->performance < $totalPenurunanPerformance) {
                    $status = 'error';
                    $msg = 'Performance mesin ' . $timMesin->name . ' tidak mampu untuk memproduksi banyak produk yang diinginkan!';
                    return response()->json(array(
                        'status' => $status,
                        'msg' => $msg,
                    ), 200);
                }
            }

            // Kalkulasi banyak udang yang dibutuhkan
            // 4 Udang Kaleng = 1 kg udang, 1 liter air mineral, 1 bungkus garam, dan 1 bungkus gula

            // Inisiasi air, garam, dan gula
            $team_waters = $this->getIngridient($team, 5);
            $team_salts = $this->getIngridient($team, 6);
            $team_sugars = $this->getIngridient($team, 7);

            // Team tidak memiliki Ingridient / null
            if ($team_waters == null || $team_salts == null || $team_sugars == null) {
                $status = 'error';
                $msg = 'Ingridient yang anda miliki tidak lengkap untuk melakukan produksi ' . $product->name . '!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Ambil total water yang dimiliki 
            $total_water = $this->sumIngridient($team, 5);
            // Ambil total salt yang dimiliki
            $total_salt = $this->sumIngridient($team, 6);
            // Ambil total sugar yang dimiliki
            $total_sugar = $this->sumIngridient($team, 7);

            // Buat Variabel utk simpan nama_ingridient
            $nama_ingridient = '';
            // Cek apakah total shrimp, water, salt, sugar cukup untuk produksi?
            if ($total_shrimp < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = Ingridient::find($tipe_udang)->name;
            }
            if ($total_water < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Air Mineral';
            }
            if ($total_salt < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Garam';
            }
            if ($total_sugar < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Gula';
            }

            //Kalau tidak cukup return error
            if ($ingridient_insufficient) {
                $status = 'error';
                $msg = 'Ingridient ' . $nama_ingridient . ' yang dimiliki tidak cukup untuk melakukan produksi!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            $berhasil_diproduksi = 0;
            // selama yang berhasil diproduksi masih kurang dari banyak produksi, lakukan produksi terus

            //Buat variabel index
            $shrimp_index = 0;
            $water_index = 0;
            $salt_index = 0;
            $sugar_index = 0;

            //Ambil ingridient yang paling atas/yang expirednya paling deket
            $shrimp_use_now = $team_shrimps[$shrimp_index]->amount_have; // index 0
            $water_use_now = $team_waters[$water_index]->amount_have; // index 0
            $salt_use_now = $team_salts[$salt_index]->amount_have; // index 0
            $sugar_use_now = $team_sugars[$sugar_index]->amount_have; // index 0

            //Buat variabel untuk tampung maksimal index dari tiap ingridient
            $shrimp_count_max = count($team_shrimps);
            $water_count_max = count($team_waters);
            $salt_count_max = count($team_salts);
            $sugar_count_max = count($team_sugars);

            //Ambil ingridient_now yang paling sedikit
            $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
            // PRODUKSI MULAI
            while ($berhasil_diproduksi < $banyak_produksi) {

                //Cek apakah ingridient paling sedikit lebih dari banyak produksi 
                if ($min_ingridient_now > ($banyak_produksi - $berhasil_diproduksi)) {
                    //Kalau lebih ubah minimalnya jadi sama dengan banyak produksi
                    $min_ingridient_now = ($banyak_produksi - $berhasil_diproduksi);
                }

                //Kurangi ingridient_now dengan nilai yang paling sedikit
                $shrimp_use_now -= $min_ingridient_now;
                $water_use_now -= $min_ingridient_now;
                $salt_use_now -= $min_ingridient_now;
                $sugar_use_now -= $min_ingridient_now;

                // Set ingridient ada sisanya
                $shrimp_remains = true;
                $water_remains = true;
                $salt_remains = true;
                $sugar_remains = true;

                $berhasil_diproduksi += $min_ingridient_now;

                //Kalau ingridient_now habis
                if ($shrimp_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_shrimps[$shrimp_index]->ingridient_id,
                        $team_shrimps[$shrimp_index]->expired_time,
                        $team_shrimps[$shrimp_index]->amount_use + $team_shrimps[$shrimp_index]->amount_have
                    );

                    $shrimp_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($shrimp_index + 1 < $shrimp_count_max) {
                        //Kalau iya naikan indexnya
                        $shrimp_index += 1;
                        //Perbaruhi ingridient_now
                        $shrimp_use_now = $team_shrimps[$shrimp_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($water_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_waters[$water_index]->ingridient_id,
                        $team_waters[$water_index]->expired_time,
                        $team_waters[$water_index]->amount_use + $team_waters[$water_index]->amount_have
                    );

                    $water_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($water_index + 1 < $water_count_max) {
                        //Kalau iya naikan indexnya
                        $water_index += 1;
                        //Perbaruhi ingridient_now
                        $water_use_now = $team_waters[$water_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($salt_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_salts[$salt_index]->ingridient_id,
                        $team_salts[$salt_index]->expired_time,
                        $team_salts[$salt_index]->amount_use + $team_salts[$salt_index]->amount_have
                    );

                    $salt_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($salt_index + 1 < $salt_count_max) {
                        //Kalau iya naikan indexnya
                        $salt_index += 1;
                        //Perbaruhi ingridient_now
                        $salt_use_now = $team_salts[$salt_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($sugar_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_sugars[$sugar_index]->ingridient_id,
                        $team_sugars[$sugar_index]->expired_time,
                        $team_sugars[$sugar_index]->amount_use + $team_sugars[$sugar_index]->amount_have
                    );

                    $sugar_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($sugar_index + 1 < $sugar_count_max) {
                        //Kalau iya naikan indexnya
                        $sugar_index += 1;
                        //Perbaruhi ingridient_now
                        $sugar_use_now = $team_sugars[$sugar_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
            }
            // PRODUKSI SELESAI (END WHILE)
            // Update amount have dan amount use
            if ($shrimp_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_shrimps[$shrimp_index]->ingridient_id,
                    $team_shrimps[$shrimp_index]->expired_time,
                    $shrimp_use_now,
                    $team_shrimps[$shrimp_index]->amount_use + ($team_shrimps[$shrimp_index]->amount_have - $shrimp_use_now)
                );
            }
            if ($water_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_waters[$water_index]->ingridient_id,
                    $team_waters[$water_index]->expired_time,
                    $water_use_now,
                    $team_waters[$water_index]->amount_use + ($team_waters[$water_index]->amount_have - $water_use_now)
                );
            }
            if ($salt_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_salts[$salt_index]->ingridient_id,
                    $team_salts[$salt_index]->expired_time,
                    $salt_use_now,
                    $team_salts[$salt_index]->amount_use + ($team_salts[$salt_index]->amount_have - $salt_use_now)
                );
            }
            if ($sugar_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_sugars[$sugar_index]->ingridient_id,
                    $team_sugars[$sugar_index]->expired_time,
                    $sugar_use_now,
                    $team_sugars[$sugar_index]->amount_use + ($team_sugars[$sugar_index]->amount_have - $sugar_use_now)
                );
            }

            //PERHITUNGAN HEAD DAN SKIN SHRIMP
            //Buat variabel untuk simpan limbah head dan limbah skin
            $wasteHead = 0;
            $wasteSkin = 0;
            //Hitung nilai tetap semisal team punya head/skin pealer
            if ($tipe_udang == 1) {
                $wasteHead = $banyak_produksi * 0.25; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 0.15; //nanti kesimpannya dalam gram!
            } else if ($tipe_udang == 2) {
                $wasteHead = $banyak_produksi * 0.3; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 0.2; //nanti kesimpannya dalam gram!
            } else if ($tipe_udang == 3) {
                $wasteHead = $banyak_produksi * 0.2; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 0.1; //nanti kesimpannya dalam gram!
            }

            // Waktu di Surabaya sekarang
            $start = DB::select(DB::raw("SELECT CURRENT_TIMESTAMP() as waktu"))[0]->waktu;
            // Tambah 20 menit waktu di surabaya sekarang
            $expired_time = date('Y-m-d H:i:s', strtotime('+20 minutes', strtotime($start)));

            //CEK MESIN HEAD/SKIN PEALER

            // Kalau punya head pealer
            $team_head_pealer = $team->teamMachines->where('machine_id', 6)->where('is_used', 1)->first();
            if ($team_head_pealer != null) {
                //Buat data kepala udang baru
                // Tambahkan kulit udang (dalam gram)
                $team->ingridients()->attach(11, [
                    'amount_have' => $wasteHead,
                    'expired_time' => $expired_time,
                    'amount_use' => 0,
                    'total' => 0,
                ]);
                //Set waste dari kepala 0 karena wastenya jadi ingridient kepala udang 
                $wasteHead = 0;
            }

            // Kalau punya skin pealer
            $team_skin_pealer = $team->teamMachines->where('machine_id', 7)->where('is_used', 1)->first();
            if ($team_skin_pealer != null) {
                //Buat data kulit udang baru
                // Tambahkan kulit udang (dalam gram)
                $team->ingridients()->attach(12, [
                    'amount_have' => $wasteSkin,
                    'expired_time' => $expired_time,
                    'amount_use' => 0,
                    'total' => 0,
                ]);
                //Set waste dari kulit 0 karena wastenya jadi ingridient kulit udang
                $wasteSkin = 0;
            }

            //PROSES HITUNG PRODUK CACAT
            //Buat variabel pembantu untuk Menghitung produk cacat
            $hasil_setelah_defect = $banyak_item;
            //Buat variabel penampung produk cacat
            $total_defect = 0;

            //$mesin_dalam_kombinasi adalah 
            //data mesin-mesin (asli) yang digunakan sama tim pada kombinasinya kaleng udang
            foreach ($mesin_dalam_kombinasis as $mesin_dalam_kombinasi) {
                //Ambil Team Machine yang idnya sama dengan $mesin_dalam_kombinasi dan is_usednya 1
                $timMesin = $team->teamMachines
                    ->where('machine_id', $mesin_dalam_kombinasi->id)
                    ->where('is_used', 1)
                    ->first();

                //Hitung defect timMesinnya
                $defect = 100 - $timMesin->performance;
                //Proses hitung total wastenya dan hasil produk akhir
                $hasil_setelah_defect = $hasil_setelah_defect - $hasil_setelah_defect * ($defect / 100);
            }
            //Total produk cacat yang nanti masuk waste (dibulatin keatas)
            $total_defect = ceil($banyak_item - $hasil_setelah_defect); //Ini nanti masuk waste

            //PROSES PENGURANGAN PERFORMA MESIN
            foreach ($mesin_dalam_kombinasis as $team_mesin) {
                //Ambil Team Machine yang idnya sama dengan $mesin_dalam_kombinasi dan is_usednya 1
                $timMesin = $team->teamMachines
                    ->where('machine_id', $team_mesin->id)
                    ->where('is_used', 1)
                    ->first();
                //Hitung kelipatan untuk menghitung penurunan performance mesin
                $kelipatan = floor(($banyak_item + $timMesin->product_produced) / 12);
                //Hitung totalPenurunanPerformance
                $totalPenurunanPerformance = $kelipatan * $kenaikan;
                //Kurangkan performance tiap mesin
                $timMesin->performance = $timMesin->performance - $totalPenurunanPerformance;
                //Hitung sisa produksi yang nanti akan memperbaruhi productProduced tiap mesin
                $sisaProduksi = ($banyak_item + $timMesin->product_produced) % 12;
                //Update product produce
                $timMesin->product_produced = $sisaProduksi;
                //Lakukan save
                $timMesin->save();

                //CEK kalau performancenya 0 akan dihapus lgsg di databasenya
                if ($timMesin->performance == 0) {
                    $this->deleteMachine($timMesin->machine_id, $team->id);
                    // Lepas Kombinasi
                    DB::statement("DELETE FROM `team_machine_combination` WHERE machine_combination_id <= 100 AND team_machine_id = " . $team->id);
                    // Lepas is_used
                    DB::statement("UPDATE `team_machines` SET is_used = 0 WHERE team_id = " . $team->id . " AND NOT (machine_id = 2 OR machine_id = 4 OR machine_id = 11 OR machine_id = 12 OR machine_id >= 15)");
                    // Pesan Harap susun mesin ulang
                    $trashMsg = 'Terdapat mesin yang performancenya 0 sehingga mesin akan langsung dibuang. Team diharapkan menyusun mesin kembali';
                }
            }

            // PRODUK DIBUAT DAN DISIMPAN KE DATABASE
            //Hasil produk akhir setelah proses defect (dibulatin kebawah)
            $hasil_produk_akhir = floor($hasil_setelah_defect);
            if ($hasil_produk_akhir >= 1) {
                $msg = 'Produksi berhasil dilakukan! ' . $hasil_produk_akhir . ' ' . $product->name . ' berhasil diproduksi ' . $trashMsg;

                $udang_kaleng_team = $team->products->where('id', $product->id)->first();
                // Apabila punya udang kaleng sebelumnya, tambahkan amount havenya dengan yang diproduksi
                if ($udang_kaleng_team != null) {
                    $hasil_produk_akhir = $hasil_produk_akhir + $udang_kaleng_team->pivot->amount_have;
                }
                $team->products()->sync([$product->id => ['amount_have' => $hasil_produk_akhir]], false);

                $status = 'success';
            } else {
                $status = 'error';
                $msg = 'Semua produk yang dihasilkan cacat akibat defect mesin yang tinggi!';
            }

            // HITUNG TOTAL LIMBAH/WASTE DAN SIMPAN KE DATABASE
            // Limbah air dihitung setiap melakukan klik button
            // CEK MESIN FILTER
            $mesin_filter = $team->teamMachines->where('machine_id', 2)->where('is_used', 1)->first();
            $limbah_air = 1;
            if ($mesin_filter != null) {
                $limbah_air = 0.5;
            }

            $team->waste = $team->waste + $total_defect + $wasteHead + $wasteSkin + $limbah_air;
            $team->save();

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        // Produksi Kitosan 102
        else if ($product->id == 2) {
            // Banyak produksi sama dengan banyak item yang diinput oleh team
            $banyak_produksi = $banyak_item;

            // Ambil kombinasi machine udang kaleng yang digunakan oleh team saat ini
            $team_machine_combination = $team->machineCombinations
                ->where('id', '102')
                ->first();

            // dd($team_machine_combination);
            // Team tidak memiliki kombinasi mesin yang sesuai
            if ($team_machine_combination == null) {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Kalkulasi banyak ingridient yang dibutuhkan

            // Inisiasi kulit udang, naoh, dan hcl
            $team_shrimp_skins = $this->getIngridient($team, 12);
            $team_naohs = $this->getIngridient($team, 9);
            $team_hcls = $this->getIngridient($team, 10);

            // Team tidak memiliki Ingridient / null
            if ($team_shrimp_skins == null || $team_naohs == null || $team_hcls == null) {
                $status = 'error';
                $msg = 'Ingridient yang anda miliki tidak lengkap untuk melakukan produksi ' . $product->name . '!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Ambil total shrimp skin yang dimiliki 
            $total_shrimp_skin = $this->sumIngridient($team, 12);
            // Ambil total naoh yang dimiliki
            $total_naoh = $this->sumIngridient($team, 9);
            // Ambil total hcl yang dimiliki
            $total_hcl = $this->sumIngridient($team, 10);

            // Buat Variabel utk simpan nama_ingridient
            $nama_ingridient = '';
            // Cek apakah total kulit udang, naoh, hcl cukup untuk produksi?
            // 1000 gram kulit udang, 1 bungkus NaoH, dan 1 bungkus Hcl
            if ($total_shrimp_skin < ($banyak_produksi)) { //Karena kulit disimpan dalam gram
                $ingridient_insufficient = true;
                $nama_ingridient = 'Kulit Udang';
            }
            if ($total_naoh < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'NAOH';
            }
            if ($total_hcl < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'HCL';
            }

            //Kalau tidak cukup return error
            if ($ingridient_insufficient) {
                $status = 'error';
                $msg = 'Ingridient ' . $nama_ingridient . ' yang dimiliki tidak cukup untuk melakukan produksi!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            //Buat variabel untuk proses produksi
            $berhasil_diproduksi = 0;
            // selama yang berhasil diproduksi masih kurang dari banyak produksi, lakukan produksi terus

            //Buat variabel index
            $shrimp_skin_index = 0;
            $naoh_index = 0;
            $hcl_index = 0;

            //Ambil ingridient yang paling atas/yang expirednya paling deket
            $shrimp_skin_use_now = $team_shrimp_skins[$shrimp_skin_index]->amount_have; // index 0
            $naoh_use_now = $team_naohs[$naoh_index]->amount_have; // index 0
            $hcl_use_now = $team_hcls[$hcl_index]->amount_have; // index 0

            //Buat variabel untuk tampung maksimal index dari tiap ingridient
            $shrimp_skin_count_max = count($team_shrimp_skins);
            $naoh_count_max = count($team_naohs);
            $hcl_count_max = count($team_hcls);

            //Ambil ingridient_now yang paling sedikit
            $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);

            // PRODUKSI MULAI
            while ($berhasil_diproduksi < $banyak_produksi) {

                //Cek apakah ingridient paling sedikit lebih dari banyak produksi 
                if ($min_ingridient_now > ($banyak_produksi - $berhasil_diproduksi)) {
                    //Kalau lebih ubah minimalnya jadi sama dengan banyak produksi
                    $min_ingridient_now = ($banyak_produksi - $berhasil_diproduksi);
                }

                //Kurangi ingridient_now dengan nilai yang paling sedikit
                $shrimp_skin_use_now -= $min_ingridient_now;
                $naoh_use_now -= $min_ingridient_now;
                $hcl_use_now -= $min_ingridient_now;

                // Set ingridient ada sisanya
                $shrimp_skin_remains = true;
                $naoh_remains = true;
                $hcl_remains = true;

                $berhasil_diproduksi += $min_ingridient_now;
                //Kalau ingridient_now habis
                if ($shrimp_skin_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_shrimp_skins[$shrimp_skin_index]->ingridient_id,
                        $team_shrimp_skins[$shrimp_skin_index]->expired_time,
                        $team_shrimp_skins[$shrimp_skin_index]->amount_use + $team_shrimp_skins[$shrimp_skin_index]->amount_have
                    );

                    $shrimp_skin_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($shrimp_skin_index + 1 < $shrimp_skin_count_max) {
                        //Kalau iya naikan indexnya
                        $shrimp_skin_index += 1;
                        //Perbaruhi ingridient_now
                        $shrimp_skin_use_now = $team_shrimp_skins[$shrimp_skin_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);
                    }
                }
                if ($naoh_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_naohs[$naoh_index]->ingridient_id,
                        $team_naohs[$naoh_index]->expired_time,
                        $team_naohs[$naoh_index]->amount_use + $team_naohs[$naoh_index]->amount_have
                    );

                    $naoh_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($naoh_index + 1 < $naoh_count_max) {
                        //Kalau iya naikan indexnya
                        $naoh_index += 1;
                        //Perbaruhi ingridient_now
                        $naoh_use_now = $team_naohs[$naoh_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);
                    }
                }
                if ($hcl_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_hcls[$hcl_index]->ingridient_id,
                        $team_hcls[$hcl_index]->expired_time,
                        $team_hcls[$hcl_index]->amount_use + $team_hcls[$hcl_index]->amount_have
                    );

                    $hcl_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($hcl_index + 1 < $hcl_count_max) {
                        //Kalau iya naikan indexnya
                        $hcl_index += 1;
                        //Perbaruhi ingridient_now
                        $hcl_use_now = $team_hcls[$hcl_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);
                    }
                }
            }
            // PRODUKSI SELESAI (END WHILE)

            // Update amount have dan amount use
            if ($shrimp_skin_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_shrimp_skins[$shrimp_skin_index]->ingridient_id,
                    $team_shrimp_skins[$shrimp_skin_index]->expired_time,
                    $shrimp_skin_use_now,
                    $team_shrimp_skins[$shrimp_skin_index]->amount_use + ($team_shrimp_skins[$shrimp_skin_index]->amount_have - $shrimp_skin_use_now)
                );
            }
            if ($naoh_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_naohs[$naoh_index]->ingridient_id,
                    $team_naohs[$naoh_index]->expired_time,
                    $naoh_use_now,
                    $team_naohs[$naoh_index]->amount_use + ($team_naohs[$naoh_index]->amount_have - $naoh_use_now)
                );
            }
            if ($hcl_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_hcls[$hcl_index]->ingridient_id,
                    $team_hcls[$hcl_index]->expired_time,
                    $hcl_use_now,
                    $team_hcls[$hcl_index]->amount_use + ($team_hcls[$hcl_index]->amount_have - $hcl_use_now)
                );
            }

            // PRODUK DIBUAT DAN DISIMPAN KE DATABASE
            $hasil_produk_akhir = $banyak_produksi;
            $kitosan = $team->products->where('id', $product->id)->first();

            // Apabila punya udang kaleng sebelumnya, tambahkan amount havenya dengan yang diproduksi
            if ($kitosan != null) {
                $hasil_produk_akhir = $hasil_produk_akhir + $kitosan->pivot->amount_have;
            }
            $team->products()->sync([$product->id => ['amount_have' => $hasil_produk_akhir]], false);

            $status = 'success';
            $msg = 'Produksi berhasil dilakukan! ' . $banyak_produksi . ' ' . $product->name . ' berhasil diproduksi';

            // HITUNG TOTAL LIMBAH/WASTE DAN SIMPAN KE DATABASE
            // Limbah air dihitung setiap melakukan klik button
            // CEK MESIN FILTER
            $mesin_filter = $team->teamMachines->where('machine_id', 2)->where('is_used', 1)->first();
            $limbah_air = 1;
            if ($mesin_filter != null) {
                $limbah_air = 0.5;
            }

            //UPDATE TEAM WASTE DI DATABASE
            $team->waste = $team->waste + $limbah_air;
            $team->save();

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        // Produksi Saos Tomat 101
        else if ($product->id == 3) {
            // Banyak produksi sama dengan banyak item yang diinput oleh team
            $banyak_produksi = $banyak_item;
            // Ambil kombinasi machine udang kaleng yang digunakan oleh team saat ini
            $team_machine_combination = $team->machineCombinations
                ->where('id', '101')
                ->first();

            // Team tidak memiliki kombinasi mesin yang sesuai
            if ($team_machine_combination == null) {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Kalkulasi banyak ingridient yang dibutuhkan

            // Inisiasi kepala udang, tomat, gula, garam, MSG
            $team_shrimp_heads = $this->getIngridient($team, 11);
            $team_tomats = $this->getIngridient($team, 4);
            $team_msgs = $this->getIngridient($team, 8);
            $team_salts = $this->getIngridient($team, 6);
            $team_sugars = $this->getIngridient($team, 7);

            // Team tidak memiliki Ingridient / null
            if ($team_shrimp_heads == null || $team_tomats == null || $team_msgs == null || $team_salts == null || $team_sugars == null) {
                $status = 'error';
                $msg = 'Ingridient yang anda miliki tidak lengkap untuk melakukan produksi ' . $product->name . '!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Ambil total shrimp head yang dimiliki 
            $total_shrimp_head = $this->sumIngridient($team, 11);
            // Ambil total tomat yang dimiliki 
            $total_tomat = $this->sumIngridient($team, 4);
            // Ambil total msg yang dimiliki 
            $total_msg = $this->sumIngridient($team, 8);
            // Ambil total salt yang dimiliki 
            $total_salt = $this->sumIngridient($team, 6);
            // Ambil total sugar yang dimiliki 
            $total_sugar = $this->sumIngridient($team, 7);

            // Buat Variabel utk simpan nama_ingridient
            $nama_ingridient = '';
            // Cek apakah total kepala udang, tomat, gula, garam, msg cukup untuk produksi?
            // 1000 gram kepala udang, 1 kg tomat, 1 bungkus gula, 1 bungkus garam, dan 1 bungkus msg
            if ($total_shrimp_head < ($banyak_produksi)) { //Karena kepala disimpan dalam gram
                $ingridient_insufficient = true;
                $nama_ingridient = 'Kepala Udang';
            }
            if ($total_tomat < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Tomat';
            }
            if ($total_msg < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'MSG';
            }
            if ($total_sugar < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Gula';
            }
            if ($total_salt < $banyak_produksi) {
                $ingridient_insufficient = true;
                $nama_ingridient = 'Garam';
            }

            //Kalau tidak cukup return error
            if ($ingridient_insufficient) {
                $status = 'error';
                $msg = 'Ingridient ' . $nama_ingridient . ' yang dimiliki tidak cukup untuk melakukan produksi!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            //Buat variabel untuk proses produksi
            $berhasil_diproduksi = 0;
            // selama yang berhasil diproduksi masih kurang dari banyak produksi, lakukan produksi terus

            //Buat variabel index
            $shrimp_head_index = 0;
            $salt_index = 0;
            $sugar_index = 0;
            $tomat_index = 0;
            $msg_index = 0;

            //Ambil ingridient yang paling atas/yang expirednya paling deket
            $shrimp_head_use_now = $team_shrimp_heads[$shrimp_head_index]->amount_have; // index 0
            $salt_use_now = $team_salts[$salt_index]->amount_have; // index 0
            $sugar_use_now = $team_sugars[$sugar_index]->amount_have; // index 0
            $tomat_use_now = $team_tomats[$tomat_index]->amount_have;
            $msg_use_now = $team_msgs[$msg_index]->amount_have;

            //Buat variabel untuk tampung maksimal index dari tiap ingridient
            $shrimp_head_count_max = count($team_shrimp_heads);
            $salt_count_max = count($team_salts);
            $sugar_count_max = count($team_sugars);
            $tomat_count_max = count($team_tomats);
            $msg_count_max = count($team_msgs);

            //Ambil ingridient_now yang paling sedikit
            $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
            // PRODUKSI MULAI
            while ($berhasil_diproduksi < $banyak_produksi) {

                //Cek apakah ingridient paling sedikit lebih dari banyak produksi 
                if ($min_ingridient_now > ($banyak_produksi - $berhasil_diproduksi)) {
                    //Kalau lebih ubah minimalnya jadi sama dengan banyak produksi
                    $min_ingridient_now = ($banyak_produksi - $berhasil_diproduksi);
                }

                //Kurangi ingridient_now dengan nilai yang paling sedikit
                $shrimp_head_use_now -= $min_ingridient_now;
                $salt_use_now -= $min_ingridient_now;
                $sugar_use_now -= $min_ingridient_now;
                $tomat_use_now -= $min_ingridient_now;
                $msg_use_now -= $min_ingridient_now;

                // Set ingridient ada sisanya
                $shrimp_head_remains = true;
                $salt_remains = true;
                $sugar_remains = true;
                $tomat_remains = true;
                $msg_remains = true;

                $berhasil_diproduksi += $min_ingridient_now;

                //Kalau ingridient_now habis
                if ($shrimp_head_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_shrimp_heads[$shrimp_head_index]->ingridient_id,
                        $team_shrimp_heads[$shrimp_head_index]->expired_time,
                        $team_shrimp_heads[$shrimp_head_index]->amount_use + $team_shrimp_heads[$shrimp_head_index]->pivot->amount_have
                    );

                    $shrimp_head_remains = false;

                    //Cek apakah ada index lanjutan? 
                    if ($shrimp_head_index + 1 < $shrimp_head_count_max) {
                        //Kalau iya naikan indexnya
                        $shrimp_head_index += 1;
                        //Perbaruhi ingridient_now
                        $shrimp_head_use_now = $team_shrimp_heads[$shrimp_head_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
                    }
                }
                if ($tomat_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_tomats[$tomat_index]->ingridient_id,
                        $team_tomats[$tomat_index]->expired_time,
                        $team_tomats[$tomat_index]->amount_use + $team_tomats[$tomat_index]->amount_have
                    );

                    $naoh_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($tomat_index + 1 < $tomat_count_max) {
                        //Kalau iya naikan indexnya
                        $tomat_index += 1;
                        //Perbaruhi ingridient_now
                        $tomat_use_now = $team_tomats[$tomat_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
                    }
                }
                if ($msg_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_msgs[$msg_index]->ingridient_id,
                        $team_msgs[$msg_index]->expired_time,
                        $team_msgs[$msg_index]->amount_use + $team_msgs[$msg_index]->amount_have
                    );

                    $hcl_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($msg_index + 1 < $msg_count_max) {
                        //Kalau iya naikan indexnya
                        $msg_index += 1;
                        //Perbaruhi ingridient_now
                        $msg_use_now = $team_msgs[$msg_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
                    }
                }
                if ($salt_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_salts[$salt_index]->ingridient_id,
                        $team_salts[$salt_index]->expired_time,
                        $team_salts[$salt_index]->amount_use + $team_salts[$salt_index]->amount_have
                    );

                    $salt_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($salt_index + 1 < $salt_count_max) {
                        //Kalau iya naikan indexnya
                        $salt_index += 1;
                        //Perbaruhi ingridient_now
                        $salt_use_now = $team_salts[$salt_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
                    }
                }
                if ($sugar_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_sugars[$sugar_index]->ingridient_id,
                        $team_sugars[$sugar_index]->expired_time,
                        $team_sugars[$sugar_index]->amount_use + $team_sugars[$sugar_index]->amount_have
                    );

                    $sugar_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($sugar_index + 1 < $sugar_count_max) {
                        //Kalau iya naikan indexnya
                        $sugar_index += 1;
                        //Perbaruhi ingridient_now
                        $sugar_use_now = $team_sugars[$sugar_index]->amount_have;
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_head_use_now, $salt_use_now, $sugar_use_now, $tomat_use_now, $msg_use_now);
                    }
                }
            }
            // PRODUKSI SELESAI (END WHILE)

            // Update amount have dan amount use
            if ($shrimp_head_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_shrimp_heads[$shrimp_head_index]->ingridient_id,
                    $team_shrimp_heads[$shrimp_head_index]->expired_time,
                    $shrimp_head_use_now,
                    $team_shrimp_heads[$shrimp_head_index]->amount_use + ($team_shrimp_heads[$shrimp_head_index]->amount_have - $shrimp_head_use_now)
                );
            }
            if ($tomat_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_tomats[$tomat_index]->ingridient_id,
                    $team_tomats[$tomat_index]->expired_time,
                    $tomat_use_now,
                    $team_tomats[$tomat_index]->amount_use + ($team_tomats[$tomat_index]->amount_have - $tomat_use_now)
                );
            }
            if ($msg_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_msgs[$msg_index]->ingridient_id,
                    $team_msgs[$msg_index]->expired_time,
                    $msg_use_now,
                    $team_msgs[$msg_index]->amount_use + ($team_msgs[$msg_index]->amount_have - $msg_use_now)
                );
            }
            if ($salt_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_salts[$salt_index]->ingridient_id,
                    $team_salts[$salt_index]->expired_time,
                    $salt_use_now,
                    $team_salts[$salt_index]->amount_use + ($team_salts[$salt_index]->amount_have - $salt_use_now)
                );
            }
            if ($sugar_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_sugars[$sugar_index]->ingridient_id,
                    $team_sugars[$sugar_index]->expired_time,
                    $sugar_use_now,
                    $team_sugars[$sugar_index]->amount_use + ($team_sugars[$sugar_index]->amount_have - $sugar_use_now)
                );
            }

            // PRODUK DIBUAT DAN DISIMPAN KE DATABASE
            $hasil_produk_akhir = $banyak_produksi;
            $saus_udang = $team->products->where('id', $product->id)->first();

            // Apabila punya udang kaleng sebelumnya, tambahkan amount havenya dengan yang diproduksi
            if ($saus_udang != null) {
                $hasil_produk_akhir = $hasil_produk_akhir + $saus_udang->pivot->amount_have;
            }
            $team->products()->sync([$product->id => ['amount_have' => $hasil_produk_akhir]], false);

            $status = 'success';
            $msg = 'Produksi berhasil dilakukan! ' . $banyak_produksi . ' ' . $product->name . ' berhasil diproduksi';

            // HITUNG TOTAL LIMBAH/WASTE DAN SIMPAN KE DATABASE
            // Limbah air dihitung setiap melakukan klik button
            // CEK MESIN FILTER
            $mesin_filter = $team->teamMachines->where('machine_id', 2)->where('is_used', 1)->first();
            $limbah_air = 1;
            if ($mesin_filter != null) {
                $limbah_air = 0.5;
            }

            //UPDATE TEAM WASTE DI DATABASE
            $team->waste = $team->waste + $limbah_air;
            $team->save();

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
