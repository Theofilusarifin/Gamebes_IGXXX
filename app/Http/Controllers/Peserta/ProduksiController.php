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

class ProduksiController extends Controller
{
    public function index()
    {
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
        return $team->ingridients
            ->where('id', $ingridient_id)
            ->where('pivot.expired_time', '>=', date('Y-m-d H:i:s'))
            ->sortBy('pivot.expired_time')->all();
    }

    public function sumIngridient(Team $team, $ingridient_id)
    {
        return $team->ingridients
            ->where('id', $ingridient_id)
            ->where('pivot.expired_time', '>=', date('Y-m-d H:i:s'))
            ->sortBy('pivot.expired_time')
            ->sum("pivot.amount_have");
    }

    public function production(Request $request)
    {
        // Ambil data dari ajax
        $team = Auth::user()->team;
        $product = Product::find($request['product_id']);
        //Input user
        $banyak_item = $request['banyak_produksi']; //Kalau Kitosan kelipatan 1/2/3/4/5/6, sisanya kelipatan 4/8/12/16/20
        $tipe_udang = $request['tipe_udang']; //Id Udang yang dipilih team
        // Status dan message untuk respond
        $status = '';
        $msg = '';
        $ingridient_insufficient = false;

        // Team salah input angka ketika ingin produksi udang kaleng (1) atau saus udang (3)
        if (($product->id == 1 || $product->id == 3) && $banyak_item % 4 != 0) {
            $status = 'error';
            $msg = 'Jumlah produksi ' . $product->name . ' harus berkelipatan 4!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

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

        // Produksi Udang Kaleng
        if ($product->id == 1) {
            // Hitung banyaknya produksi dengan cara dibagi 4
            $banyak_produksi = $banyak_item / 4;
            // Ambil kombinasi machine udang kaleng yang digunakan oleh team saat ini
            $team_machine_combination = $team->machineCombinations
                ->where('id', '!=', '101')
                ->where('id', '!=', '102')
                ->all();

            $combination_total = count($team_machine_combination);
            // Team tidak memiliki kombinasi mesin yang sesuai
            if ($combination_total == 0) {
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
            if ($season_now == 1 && $mesin_ac != null) {
                $kenaikan = 10; //Pengali untuk penurunan performance
            }
            //Dapat data mesin-mesin (asli) yang digunakan sama tim pada kombinasinya kaleng udang
            $mesin_dalam_kombinasis = $team_machine_combination[0]->machines;
            // dd($team_mesins[0]);
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
            $shrimp_use_now = $team_shrimps[$shrimp_index]; // index 0
            $water_use_now = $team_waters[$water_index]; // index 0
            $salt_use_now = $team_salts[$salt_index]; // index 0
            $sugar_use_now = $team_sugars[$sugar_index]; // index 0

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
                        $team_shrimps[$shrimp_index]->pivot->ingridient_id,
                        $team_shrimps[$shrimp_index]->pivot->expired_time,
                        $team_shrimps[$shrimp_index]->pivot->amount_use + $team_shrimps[$shrimp_index]->pivot->amount_have
                    );

                    $shrimp_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($shrimp_index + 1 <= $shrimp_count_max) {
                        //Kalau iya naikan indexnya
                        $shrimp_index += 1;
                        //Perbaruhi ingridient_now
                        $shrimp_use_now = $team_shrimps[$shrimp_index];
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($water_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_waters[$water_index]->pivot->ingridient_id,
                        $team_waters[$water_index]->pivot->expired_time,
                        $team_waters[$water_index]->pivot->amount_use + $team_waters[$water_index]->pivot->amount_have
                    );

                    $water_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($water_index + 1 <= $water_count_max) {
                        //Kalau iya naikan indexnya
                        $water_index += 1;
                        //Perbaruhi ingridient_now
                        $water_use_now = $team_waters[$water_index];
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($salt_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_salts[$salt_index]->pivot->ingridient_id,
                        $team_salts[$salt_index]->pivot->expired_time,
                        $team_salts[$salt_index]->pivot->amount_use + $team_salts[$salt_index]->pivot->amount_have
                    );

                    $salt_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($salt_index + 1 <= $salt_count_max) {
                        //Kalau iya naikan indexnya
                        $salt_index += 1;
                        //Perbaruhi ingridient_now
                        $salt_use_now = $team_salts[$salt_index];
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_use_now, $water_use_now, $salt_use_now, $sugar_use_now);
                    }
                }
                if ($sugar_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_sugars[$sugar_index]->pivot->ingridient_id,
                        $team_sugars[$sugar_index]->pivot->expired_time,
                        $team_sugars[$sugar_index]->pivot->amount_use + $team_sugars[$sugar_index]->pivot->amount_have
                    );

                    $sugar_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($sugar_index + 1 <= $sugar_count_max) {
                        //Kalau iya naikan indexnya
                        $sugar_index += 1;
                        //Perbaruhi ingridient_now
                        $sugar_use_now = $team_sugars[$sugar_index];
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
                    $team_shrimps[$shrimp_index]->pivot->ingridient_id,
                    $team_shrimps[$shrimp_index]->pivot->expired_time,
                    $shrimp_use_now,
                    $team_shrimps[$shrimp_index]->pivot->amount_use - $shrimp_use_now
                );
            }
            if ($water_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_waters[$water_index]->pivot->ingridient_id,
                    $team_waters[$water_index]->pivot->expired_time,
                    $water_use_now,
                    $team_waters[$water_index]->pivot->amount_use - $water_use_now
                );
            }
            if ($salt_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_salts[$salt_index]->pivot->ingridient_id,
                    $team_salts[$salt_index]->pivot->expired_time,
                    $salt_use_now,
                    $team_salts[$salt_index]->pivot->amount_use - $salt_use_now
                );
            }
            if ($sugar_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_sugars[$sugar_index]->pivot->ingridient_id,
                    $team_sugars[$sugar_index]->pivot->expired_time,
                    $sugar_use_now,
                    $team_sugars[$sugar_index]->pivot->amount_use - $sugar_use_now
                );
            }

            //PERHITUNGAN HEAD DAN SKIN SHRIMP
            //Buat variabel untuk simpan limbah head dan limbah skin
            $wasteHead = 0;
            $wasteSkin = 0;
            //Hitung nilai tetap semisal team punya head/skin pealer
            if ($tipe_udang == 1) {
                $wasteHead = $banyak_produksi * 250; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 150; //nanti kesimpannya dalam gram!
            } else if ($tipe_udang == 2) {
                $wasteHead = $banyak_produksi * 300; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 200; //nanti kesimpannya dalam gram!
            } else if ($tipe_udang == 3) {
                $wasteHead = $banyak_produksi * 200; //nanti kesimpannya dalam gram!
                $wasteSkin = $banyak_produksi * 100; //nanti kesimpannya dalam gram!
            }

            // Waktu di Surabaya sekarang
            $start = date('Y-m-d H:i:s');
            // Tambah 8 menit waktu di surabaya sekarang
            $expired_time = date('Y-m-d H:i:s', strtotime('+8 minutes', strtotime($start)));

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

            //PROSES PENGURANGAN PEFORMA MESIN
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
            }

            // PRODUK DIBUAT DAN DISIMPAN KE DATABASE
            //Hasil produk akhir setelah proses defect (dibulatin kebawah)
            $hasil_produk_akhir = floor($hasil_setelah_defect);
            if ($hasil_produk_akhir > 0) {
                $udang_kaleng_team = $team->products->where('id', $product->id)->first();

                // Apabila punya udang kaleng sebelumnya, tambahkan amount havenya dengan yang diproduksi
                if ($udang_kaleng_team != null) {
                    $hasil_produk_akhir = $hasil_produk_akhir + $udang_kaleng_team->amount_have;
                }
                $team->products()->sync([$product->id => ['amount_have' => $hasil_produk_akhir]], false);

                $status = 'error';
                $msg = 'Produksi berhasil dilakukan! ' . $hasil_produk_akhir . ' ' . $product->name . ' berhasil diproduksi';
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

            //UPDATE TEAM WASTE DI DATABASE
            $team->waste = $team->waste + $total_defect + $wasteHead + $wasteSkin + $limbah_air;
            $team->save();

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        // Produksi Kitosan
        else if ($product->id == 2) {
            // Banyak produksi sama dengan banyak item yang diinput oleh team
            $banyak_produksi = $banyak_item;
            // Ambil kombinasi machine udang kaleng yang digunakan oleh team saat ini
            $team_machine_combination = $team->machineCombinations
                ->where('id', '101')
                ->all();

            $combination_total = count($team_machine_combination);
            // Team tidak memiliki kombinasi mesin yang sesuai
            if ($combination_total == 0) {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }

            // Kalkulasi banyak ingridient yang dibutuhkan
            // 1000 gram kulit udang, 1 bungkus NaoH, dan 1 bungkus Hcl

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

            // Ambil total shrim skim yang dimiliki 
            $total_shrimp_skin = $this->sumIngridient($team, 12);
            // Ambil total naoh yang dimiliki
            $total_naoh = $this->sumIngridient($team, 9);
            // Ambil total hcl yang dimiliki
            $total_hcl = $this->sumIngridient($team, 10);

            // Buat Variabel utk simpan nama_ingridient
            $nama_ingridient = '';
            // Cek apakah total kulit udang, naoh, hcl cukup untuk produksi?
            if ($total_shrimp_skin < $banyak_produksi) {
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

            $berhasil_diproduksi = 0;
            // selama yang berhasil diproduksi masih kurang dari banyak produksi, lakukan produksi terus

            //Buat variabel index
            $shrimp_skin_index = 0;
            $naoh_index = 0;
            $hcl_index = 0;

            //Ambil ingridient yang paling atas/yang expirednya paling deket
            $shrimp_skin_use_now = $team_shrimp_skins[$shrimp_skin_index]; // index 0
            $naoh_use_now = $team_naohs[$naoh_index]; // index 0
            $hcl_use_now = $team_hcls[$hcl_index]; // index 0

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
                        $team_shrimp_skins[$shrimp_skin_index]->pivot->ingridient_id,
                        $team_shrimp_skins[$shrimp_skin_index]->pivot->expired_time,
                        $team_shrimp_skins[$shrimp_skin_index]->pivot->amount_use + $team_shrimp_skins[$shrimp_skin_index]->pivot->amount_have
                    );

                    $shrimp_skin_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($shrimp_skin_index + 1 <= $shrimp_skin_count_max) {
                        //Kalau iya naikan indexnya
                        $shrimp_skin_index += 1;
                        //Perbaruhi ingridient_now
                        $shrimp_use_now = $team_shrimp_skins[$shrimp_skin_index];
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);
                    }
                }
                if ($naoh_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_naohs[$naoh_index]->pivot->ingridient_id,
                        $team_naohs[$naoh_index]->pivot->expired_time,
                        $team_naohs[$naoh_index]->pivot->amount_use + $team_naohs[$naoh_index]->pivot->amount_have
                    );

                    $naoh_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($naoh_index + 1 <= $naoh_count_max) {
                        //Kalau iya naikan indexnya
                        $naoh_index += 1;
                        //Perbaruhi ingridient_now
                        $naoh_use_now = $team_naohs[$naoh_index];
                        //Perbaruhi nilai yang paling sedikit
                        $min_ingridient_now = min($shrimp_skin_use_now, $naoh_use_now, $hcl_use_now);
                    }
                }
                if ($hcl_use_now == 0) {
                    //Hapus data yang ada di database
                    $this->usedAllIngridient(
                        $team->id,
                        $team_hcls[$hcl_index]->pivot->ingridient_id,
                        $team_hcls[$hcl_index]->pivot->expired_time,
                        $team_hcls[$hcl_index]->pivot->amount_use + $team_hcls[$hcl_index]->pivot->amount_have
                    );

                    $hcl_remains = false;
                    //Cek apakah ada index lanjutan? 
                    if ($hcl_index + 1 <= $hcl_count_max) {
                        //Kalau iya naikan indexnya
                        $hcl_index += 1;
                        //Perbaruhi ingridient_now
                        $hcl_use_now = $team_hcls[$hcl_index];
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
                    $team_shrimp_skins[$shrimp_skin_index]->pivot->ingridient_id,
                    $team_shrimp_skins[$shrimp_skin_index]->pivot->expired_time,
                    $shrimp_skin_use_now,
                    $team_shrimp_skins[$shrimp_skin_index]->pivot->amount_use - $shrimp_skin_use_now
                );
            }
            if ($naoh_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_naohs[$naoh_index]->pivot->ingridient_id,
                    $team_naohs[$naoh_index]->pivot->expired_time,
                    $naoh_use_now,
                    $team_naohs[$naoh_index]->pivot->amount_use - $naoh_use_now
                );
            }
            if ($hcl_remains) {
                $this->updateIngridientUsed(
                    $team->id,
                    $team_hcls[$hcl_index]->pivot->ingridient_id,
                    $team_hcls[$hcl_index]->pivot->expired_time,
                    $hcl_use_now,
                    $team_hcls[$hcl_index]->pivot->amount_use - $hcl_use_now
                );
            }
            //     // Ambil kombinasi machine udang kaleng dari team
            //     $team_machine_combination = $team->machineCombinations->where('id', '101')->first();
            //     $combination_total = 0;
            //     if ($team_machine_combination != null) {
            //         $combination_total = count($team_machine_combination);
            //     }
            //     // Lihat team punya kombinasi atau nggak?
            //     if ($combination_total > 0) {
            //         //Ambil bahan
            //         //Ambil Kulit Udang
            //         $kulitUdang = $team->ingridients->where('id', 12)->first();
            //         $jumlah_kulit_udang = 0;
            //         if ($kulitUdang != null) {
            //             $jumlah_kulit_udang = $team->ingridients->where('id', 12)->first()->pivot->amount_have;
            //         }
            //         //Ambil NaOH
            //         $naoh = $team->ingridients->where('id', 9)->first();
            //         $jumlah_naoh = 0;
            //         if ($naoh != null) {
            //             $jumlah_naoh = $team->ingridients->where('id', 9)->first()->pivot->amount_have;
            //         }
            //         //Ambil HCl
            //         $hcl = $team->ingridients->where('id', 10)->first();
            //         $jumlah_hcl = 0;
            //         if ($hcl != null) {
            //             $jumlah_hcl = $team->ingridients->where('id', 10)->first()->pivot->amount_have;
            //         }

            //         //Cek apakah bahan tersedia?
            //         $bahan_kulit_udang = $banyak_item * 1000; //kulit disimpan dalam satuan gram 
            //         $bahan_naoh = $banyak_item * 1; //disimpan dalam satuan bungkus
            //         $bahan_hcl = $banyak_item * 1; //disimpan dalam satuan bungkus

            //         //Bahan cukup atau tidak?
            //         if ($jumlah_kulit_udang >= $bahan_kulit_udang && $jumlah_naoh >= $bahan_naoh && $jumlah_hcl >= $bahan_hcl) {
            //             // Bahan dikuragi
            //             // Kurangi kulit udang
            //             $amount_use_kulit = $team->ingridients->where('id', 12)->first()->pivot->amount_use;
            //             $team->ingridients()->sync([12 => ['amount_have' => $jumlah_kulit_udang - $bahan_kulit_udang, 'amount_use' => $amount_use_kulit + $bahan_kulit_udang]], false);
            //             // Kurangi NAOH
            //             $amount_use_naoh = $team->ingridients->where('id', 9)->first()->pivot->amount_use;
            //             $team->ingridients()->sync([9 => ['amount_have' => $jumlah_naoh - $bahan_naoh, 'amount_use' => $amount_use_naoh + $bahan_naoh]], false);
            //             // Kurangi HCL
            //             $amount_use_hcl = $team->ingridients->where('id', 10)->first()->pivot->amount_use;
            //             $team->ingridients()->sync([10 => ['amount_have' => $jumlah_hcl - $bahan_hcl, 'amount_use' => $amount_use_hcl + $bahan_hcl]], false);

            //             //Produk ditambah
            //             $team_product = $team->products->where('id', $product->id)->first();
            //             $amount_product = 0;
            //             if ($team_product != null) {
            //                 $amount_product = $team_product->pivot->amount_have;
            //             }

            //             //Update data
            //             $team->products()->sync([$product->id => ['amount_have' => $amount_product + (1 * $banyak_item)]]);
            //             $team->save();

            //             $status = 'success';
            //             $msg = 'Produksi berhasil dilakukan!';
            //         } else {
            //             $status = 'error';
            //             $msg = 'Bahan yang dibutuhkan tidak cukup!';
            //         }
            //     }
            //     // Ga punya kombinasi mesin 
            //     else {
            //         $status = 'error';
            //         $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';
            //     }
        }
        // // Produksi Saos Tomat
        // else if ($product->id == 3) {
        //     // Ambil kombinasi machine udang kaleng dari team
        //     $team_machine_combination = $team->machineCombinations->where('id', '102')->first();
        //     $combination_total = 0;
        //     if ($team_machine_combination != null) {
        //         $combination_total = count($team_machine_combination);
        //     }
        //     // Lihat team punya kombinasi atau nggak?
        //     if ($combination_total > 0) {
        //         //Ambil bahan
        //         //Ambil Kepala Udang
        //         $kepala_udang = $team->ingridients->where('id', 11)->first();
        //         $jumlah_kepala_udang = 0;
        //         if ($kepala_udang != null) {
        //             $jumlah_kepala_udang = $team->ingridients->where('id', 11)->first()->pivot->amount_have;
        //         }
        //         //Ambil Tomat
        //         $tomat = $team->ingridients->where('id', 4)->first();
        //         $jumlah_tomat = 0;
        //         if ($tomat != null) {
        //             $jumlah_tomat = $team->ingridients->where('id', 4)->first()->pivot->amount_have;
        //         }
        //         //Ambil MSG
        //         $msg = $team->ingridients->where('id', 8)->first();
        //         $jumlah_msg = 0;
        //         if ($msg != null) {
        //             $jumlah_msg = $team->ingridients->where('id', 8)->first()->pivot->amount_have;
        //         }
        //         // Ambil garam
        //         $garam = $team->ingridients->where('id', 6)->first();
        //         $jumlah_garam = 0;
        //         if ($garam != null) {
        //             $jumlah_garam = $team->ingridients->where('id', 6)->first()->pivot->amount_have;
        //         }
        //         // Ambil gula
        //         $gula = $team->ingridients->where('id', 7)->first();
        //         $jumlah_gula = 0;
        //         if ($gula != null) {
        //             $jumlah_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_have;
        //         }

        //         //Cek apakah bahan tersedia?
        //         $bahan_kepala_udang = $banyak_item * 1000; //karena disimpannya dalam satuan gram
        //         $bahan_msg = $banyak_item * 1; //disimpannya dalam satuan bungkus
        //         $bahan_garam = $banyak_item * 1; //satuannya dalam bungkus
        //         $bahan_gula = $banyak_item * 1; //satuannya dalam bungkus
        //         $bahan_tomat = $banyak_item * 1; //satuannya dalam kg

        //         //Bahan cukup atau tidak?
        //         if (
        //             $jumlah_kepala_udang >= $bahan_kepala_udang && $jumlah_tomat >= $bahan_tomat
        //             && $jumlah_msg >= $bahan_msg && $jumlah_gula >= $bahan_gula && $jumlah_garam >= $bahan_garam
        //         ) {
        //             // Bahan dikuragi

        //             // Kurangi kulit udang
        //             $amount_use_kepala = $team->ingridients->where('id', 11)->first()->pivot->amount_use;
        //             $team->ingridients()->sync([11 => ['amount_have' => $jumlah_kepala_udang - $bahan_kepala_udang, 'amount_use' => $amount_use_kepala + $bahan_kepala_udang]], false);
        //             // Kurangi MSG
        //             $amount_use_msg = $team->ingridients->where('id', 8)->first()->pivot->amount_use;
        //             $team->ingridients()->sync([8 => ['amount_have' => $jumlah_msg - $bahan_msg, 'amount_use' => $amount_use_msg + $bahan_msg]], false);
        //             // Kurangi Tomat
        //             $amount_use_tomat = $team->ingridients->where('id', 4)->first()->pivot->amount_use;
        //             $team->ingridients()->sync([4 => ['amount_have' => $jumlah_tomat - $bahan_tomat, 'amount_use' => $amount_use_tomat + $bahan_tomat]], false);
        //             // Kurangi Garam
        //             $amount_use_garam = $team->ingridients->where('id', 6)->first()->pivot->amount_use;
        //             $team->ingridients()->sync([6 => ['amount_have' => $jumlah_garam - $bahan_garam, 'amount_use' => $amount_use_garam + $bahan_garam]], false);
        //             // Kurangi Gula
        //             $amount_use_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_use;
        //             $team->ingridients()->sync([7 => ['amount_have' => $jumlah_gula - $bahan_gula, 'amount_use' => $amount_use_gula + $bahan_gula]], false);

        //             //Produk ditambah
        //             $team_product = $team->products->where('id', $product->id)->first();
        //             $amount_product = 0;
        //             if ($team_product != null) {
        //                 $amount_product = $team_product->pivot->amount_have;
        //             }

        //             //Update data
        //             $team->products()->sync([$product->id => ['amount_have' => $amount_product + (4 * $banyak_item)]]);
        //             $team->save();

        //             $status = 'success';
        //             $msg = 'Produksi berhasil dilakukan!';
        //         }
        //         // Ga punya kombinasi mesin 
        //         else {
        //             $status = 'error';
        //             $msg = 'Bahan yang dibutuhkan tidak cukup!';
        //         }
        //     } else {
        //         $status = 'error';
        //         $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';
        //     }
        // } else {
        //     $status = 'error';
        //     $msg = 'Product yang akan diproduksi tidak valid!';
        // }
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
