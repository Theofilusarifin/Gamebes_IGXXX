<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use App\Product;
use App\Season;
use App\SeasonNow;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduksiController extends Controller
{
    public function index()
    {
        return view('peserta.produksi.index');
    }

    public function production(Request $request)
    {
        // Ambil data dari ajax
        $team = Auth::user()->team;
        $product = Product::find($request['product_id']);
        $banyak_item = $request['banyak_produksi'];
        $tipe_udang = $request['tipe_udang']; //Id Udang yang dipilih team
        // Status dan message untuk respond
        $status = '';
        $msg = '';

        // Produksi Udang Kaleng
        if ($product->id == 1) {
            // Ambil kombinasi machine udang kaleng dari team
            $team_machine_combination = $team->machineCombinations->where('id', '!=', '101')->where('id', '!=', '102')->all();
            $combination_total = count($team_machine_combination);
            // Lihat team punya kombinasi atau nggak?
            if ($combination_total > 0) {
                // Ambil bahan
                // Ambil Udang
                // $udang = $team->ingridients->where('id', $tipe_udang)->orderBy('pivot_expired_time', 'desc')->first();
                $udang_vaname = $team->ingridients->where('id', 1)->first();
                //dd($udang);
                $jumlah_udang_vaname = 0;
                if ($udang_vaname != null) {
                    $jumlah_udang_vaname = $udang_vaname->pivot->amount_have;
                }
                // Ambil Udang pama
                $udang_pama = $team->ingridients->where('id', 2)->first();
                $jumlah_udang_pama = 0;
                if ($udang_pama != null) {
                    $jumlah_udang_pama = $udang_pama->pivot->amount_have;
                }
                // Ambil Udang jerbung
                $udang_jerbung = $team->ingridients->where('id', 3)->first();
                $jumlah_udang_jerbung = 0;
                if ($udang_jerbung != null) {
                    $jumlah_udang_jerbung = $udang_jerbung->pivot->amount_have;
                }

                $array_udang = [$jumlah_udang_vaname, $jumlah_udang_pama, $jumlah_udang_jerbung];
                $jumlah_udang_terbanyak = max($array_udang);
                $index_terbanyak = array_search($jumlah_udang_terbanyak, $array_udang);

                $udang_dipakai = ''; //Udang yang dipakai buat produksi
                if ($index_terbanyak == 0) {
                    $udang_dipakai = $jumlah_udang_vaname;
                } elseif ($index_terbanyak == 1) {
                    $udang_dipakai = $jumlah_udang_pama;
                } elseif ($index_terbanyak == 2) {
                    $udang_dipakai = $jumlah_udang_jerbung;
                }

                // Ambil air mineral yang dipunya

                $air_mineral = $team->ingridients->where('id', 5)->first();
                $jumlah_air_mineral = 0;
                if ($air_mineral != null) {
                    $jumlah_air_mineral = $air_mineral->pivot->amount_have;
                }

                // Ambil garam
                $garam = $team->ingridients->where('id', 6)->first();
                $jumlah_garam = 0;
                if ($garam != null) {
                    $jumlah_garam = $garam->pivot->amount_have;
                }

                // Ambil gula
                $gula = $team->ingridients->where('id', 7)->first();
                $jumlah_gula = 0;
                if ($gula != null) {
                    $jumlah_gula = $gula->pivot->amount_have;
                }

                $bahan_udang = $banyak_item * 1; //disimpan dalam satuan kg
                $bahan_air_mineral = $banyak_item * 1; //disimpan dalam satuan liter
                $bahan_garam = $banyak_item * 1; //disimpan dalam satuan bungkus
                $bahan_gula = $banyak_item * 1; //disimpan dalam satuan bungkus

                // Kalau bahan udang cukup (bahan udang adalah udang yang dipakai)
                if ($bahan_udang <= $udang_dipakai && $bahan_air_mineral <= $jumlah_air_mineral && $bahan_garam <= $jumlah_garam && $bahan_gula <= $jumlah_gula) {
                    $banyakProduksi = 4 * $banyak_item; //Banyak hasil jadi yang diharapkan
                    //Ambil Season sekarang
                    $season_now = SeasonNow::first()->number; // 1 panas, 2 hujan, 3 dingin
                    //Ambil AC
                    $mesin_ac = $team->teamMachines->where('machine_id', 4)->where('is_used', 1)->first();
                    $kenaikan = 5;
                    if ($season_now == 1 && $mesin_ac != null) {
                        $kenaikan = 10;
                    }
                    $team_mesins = $team_machine_combination[0]->machines; //Dapat data mesin-mesin (asli) yang digunakan sama tim pada kombinasinya kaleng udang
                    // dd($team_mesins[2]->id);
                    foreach ($team_mesins as $team_mesin) {
                        //Ambil teammachine yang idnya sama dengan $team_mesin dan is_usednya 1
                        $timMesin = $team->teamMachines->where('machine_id', $team_mesin->id)->where('is_used', 1)->first();
                        $kelipatan = floor(($banyakProduksi + $timMesin->product_produced) / 12);
                        $totalKenaikanDefect = $kelipatan * $kenaikan;
                        //Cek Performance utk setiap mesin
                        if ($timMesin->performance <= $totalKenaikanDefect) {
                            $status = 'error';
                            $msg = 'Terdapat mesin yang tidak mampu untuk memproduksi';
                            return response()->json(array(
                                'status' => $status,
                                'msg' => $msg,
                            ), 200);
                        }
                    }

                    // Variabel penampung limbah kepala dan kulit yaitu 0.5 dari udang yang dipakai
                    $wasteHead = $bahan_udang / 2;
                    $wasteKulit = $bahan_udang / 2;
                    // Kalau punya head pealer
                    $team_head_pealer = $team->teamMachines->where('machine_id', 6)->first();

                    if ($team_head_pealer != null) {
                        //Menghasilkan kepala udang
                        $amount_have = $team->ingridients->where('id', 11)->first()->amount_have;
                        // Tambahkan kulit udang (dalam gram)
                        //Cek tipe udangnya
                        if ($tipe_udang == 1) {
                            //Tambahkan kulit udang sebanyak 250gr tiap 1kg udang vaname
                            $team->ingridients()->sync([11 => ['amount_have' => $amount_have + (250 * $banyak_item)]], false);
                        } else if ($tipe_udang == 2) {
                            //Tambahkan kulit udang sebanyak 300gr tiap 1kg udang pama
                            $team->ingridients()->sync([11 => ['amount_have' => $amount_have + (300 * $banyak_item)]], false);
                        } else if ($tipe_udang == 3) {
                            //Tambahkan kulit udang sebanyak 200gr tiap 1kg udang jerbung
                            $team->ingridients()->sync([11 => ['amount_have' => $amount_have + (200 * $banyak_item)]], false);
                        }
                        //Set waste dari kepala 0 karena wastenya jadi ingridient kepala udang 
                        $wasteHead = 0;
                    }
                    // Kalau punya skin pealer
                    $team_skin_pealer = $team->teamMachines->where('id', 7)->first();
                    if ($team_skin_pealer != null) {
                        //Menghasilkan kulit udang
                        $amount_have = $team->ingridients->where('id', 12)->first()->amount_have;
                        // Tambahkan kulit udang (dalam gram)
                        //Cek tipe udangnya
                        if ($tipe_udang == 1) {
                            //Tambahkan kulit udang sebanyak 150gr tiap 1kg udang vaname
                            $team->ingridients()->sync([12 => ['amount_have' => $amount_have + (150 * $banyak_item)]], false);
                        } else if ($tipe_udang == 2) {
                            //Tambahkan kulit udang sebanyak 200gr tiap 1kg udang pama
                            $team->ingridients()->sync([12 => ['amount_have' => $amount_have + (200 * $banyak_item)]], false);
                        } else if ($tipe_udang == 3) {
                            //Tambahkan kulit udang sebanyak 100gr tiap 1kg udang jerbung
                            $team->ingridients()->sync([12 => ['amount_have' => $amount_have + (100 * $banyak_item)]], false);
                        }
                        //Set waste dari kulit 0 karena wastenya jadi ingridient kulit udang
                        $wasteKulit = 0;
                    }

                    // Bahan dikuragi
                    // Kurangi udang
                    $amount_use_udang = $team->ingridients->where('id', $index_terbanyak + 1)->first()->pivot->amount_use;
                    $team->ingridients()->sync([$index_terbanyak + 1 => ['amount_have' => $udang_dipakai - $bahan_udang, 'amount_use' => $amount_use_udang + $bahan_udang]], false);
                    // Kurangi air mineral
                    $amount_use_air = $team->ingridients->where('id', 5)->first()->pivot->amount_use;
                    $team->ingridients()->sync([5 => ['amount_have' => $jumlah_air_mineral - $bahan_air_mineral, 'amount_use' => $amount_use_air + $bahan_air_mineral]], false);
                    // Kurangi Garam
                    $amount_use_garam = $team->ingridients->where('id', 6)->first()->pivot->amount_use;
                    $team->ingridients()->sync([6 => ['amount_have' => $jumlah_garam - $bahan_garam, 'amount_use' => $amount_use_garam + $bahan_garam]], false);
                    // Kurangi Gula
                    $amount_use_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_use;
                    $team->ingridients()->sync([7 => ['amount_have' => $jumlah_gula - $bahan_gula, 'amount_use' => $amount_use_gula + $bahan_gula]], false);

                    //Ambil produk yang mau di tambahin
                    $team_product = $team->products->where('id', $product->id)->first();
                    $amount_product = 0;
                    if ($team_product != null) {
                        //Kita tampung produk yang dimiliki tim saat ini
                        $amount_product = $team_product->pivot->amount_have;
                    }

                    //PROSES DEFECT
                    //Ambil semua mesin yang ada dimana team_machine_combinationnya sama dengan $team_machine_combination
                    $hasil_setelah_defect = $banyakProduksi;
                    $total_defect = 0;

                    foreach ($team_mesins as $team_mesin) {
                        //Ambil defect
                        $defect = $team_mesin->defect;
                        //Proses hitung total wastenya dan hasil produk akhir
                        $temporary_defect = $hasil_setelah_defect;
                        $hasil_setelah_defect = $hasil_setelah_defect - $hasil_setelah_defect * ($defect / 100);
                        $total_defect = $total_defect + ($temporary_defect - $hasil_setelah_defect);
                    }
                    //Hasil produk akhir setelah proses defect (dibulatin kebawah)
                    ($hasil_defect_final = floor($hasil_setelah_defect));
                    //Total defect yang nanti masuk waste (dibulatin keatas)
                    ($hasil_total_defect_final = ceil($total_defect));

                    //PENGURANGAN PEFORMA MESIN dan UPDATE PRODUCT PRODUCED
                    foreach ($team_mesins as $team_mesin) {
                        //Ambil teammachine yang idnya sama dengan $team_mesin dan is_usednya 1
                        $timMesin = $team->teamMachines->where('machine_id', $team_mesin->id)->where('is_used', 1)->first();
                        //Hitung besar kenaikannya
                        $kelipatan = floor(($banyakProduksi + $timMesin->product_produced) / 12);
                        $totalKenaikanDefect = $kelipatan * $kenaikan;
                        //Kurangkan performance tiap mesin
                        $timMesin->performance = $timMesin->performance - $totalKenaikanDefect;
                        //Hitung sisa produksi yang nanti akan dimasukan ke product produced yang Baru
                        $sisaProduksi = ($banyakProduksi + $timMesin->product_produced) % 12;
                        //Update product produce
                        $timMesin->product_produced = $sisaProduksi;
                        //Lakukan save
                        $timMesin->save();
                    }

                    //PERHITUNGAN LIMBAH AIR! 
                    //Ambil Filter
                    $mesin_filter = $team->teamMachines->where('machine_id', 2)->where('is_used', 1)->first();
                    $pengaliLimbahAir = 1;
                    if ($mesin_filter != null) {
                        $pengaliLimbahAir = 0.5;
                    }
                    $limbah_air = $banyakProduksi * $pengaliLimbahAir;

                    //UPDATE DATA
                    // Tambahkan banyak Produksi pada product team
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + ($banyakProduksi)]]);
                    $team->waste = $team->waste + ($hasil_total_defect_final) + $wasteHead + $wasteKulit + $limbah_air;
                    $team->save();

                    $status = 'success';
                    $msg = 'Produksi berhasil dilakukan!';
                } else {
                    $status = 'error';
                    $msg = 'Bahan yang dibutuhkan tidak cukup!';
                }
            }
            // Ga punya kombinasi mesin 
            else {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';
            }
        }
        // Produksi Kitosan
        else if ($product->id == 2) {
            // Ambil kombinasi machine udang kaleng dari team
            $team_machine_combination = $team->machineCombinations->where('id', '101')->first();
            $combination_total = 0;
            if ($team_machine_combination != null) {
                $combination_total = count($team_machine_combination);
            }
            // Lihat team punya kombinasi atau nggak?
            if ($combination_total > 0) {
                //Ambil bahan
                //Ambil Kulit Udang
                $kulitUdang = $team->ingridients->where('id', 12)->first();
                $jumlah_kulit_udang = 0;
                if ($kulitUdang != null) {
                    $jumlah_kulit_udang = $team->ingridients->where('id', 12)->first()->pivot->amount_have;
                }
                //Ambil NaOH
                $naoh = $team->ingridients->where('id', 9)->first();
                $jumlah_naoh = 0;
                if ($naoh != null) {
                    $jumlah_naoh = $team->ingridients->where('id', 9)->first()->pivot->amount_have;
                }
                //Ambil HCl
                $hcl = $team->ingridients->where('id', 10)->first();
                $jumlah_hcl = 0;
                if ($hcl != null) {
                    $jumlah_hcl = $team->ingridients->where('id', 10)->first()->pivot->amount_have;
                }

                //Cek apakah bahan tersedia?
                $bahan_kulit_udang = $banyak_item * 1000; //kulit disimpan dalam satuan gram 
                $bahan_naoh = $banyak_item * 1; //disimpan dalam satuan bungkus
                $bahan_hcl = $banyak_item * 1; //disimpan dalam satuan bungkus

                //Bahan cukup atau tidak?
                if ($jumlah_kulit_udang >= $bahan_kulit_udang && $jumlah_naoh >= $bahan_naoh && $jumlah_hcl >= $bahan_hcl) {
                    // Bahan dikuragi
                    // Kurangi kulit udang
                    $amount_use_kulit = $team->ingridients->where('id', 12)->first()->pivot->amount_use;
                    $team->ingridients()->sync([12 => ['amount_have' => $jumlah_kulit_udang - $bahan_kulit_udang, 'amount_use' => $amount_use_kulit + $bahan_kulit_udang]], false);
                    // Kurangi NAOH
                    $amount_use_naoh = $team->ingridients->where('id', 9)->first()->pivot->amount_use;
                    $team->ingridients()->sync([9 => ['amount_have' => $jumlah_naoh - $bahan_naoh, 'amount_use' => $amount_use_naoh + $bahan_naoh]], false);
                    // Kurangi HCL
                    $amount_use_hcl = $team->ingridients->where('id', 10)->first()->pivot->amount_use;
                    $team->ingridients()->sync([10 => ['amount_have' => $jumlah_hcl - $bahan_hcl, 'amount_use' => $amount_use_hcl + $bahan_hcl]], false);

                    //Produk ditambah
                    $team_product = $team->products->where('id', $product->id)->first();
                    $amount_product = 0;
                    if ($team_product != null) {
                        $amount_product = $team_product->pivot->amount_have;
                    }

                    //Update data
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + (1 * $banyak_item)]]);
                    $team->save();

                    $status = 'success';
                    $msg = 'Produksi berhasil dilakukan!';
                } else {
                    $status = 'error';
                    $msg = 'Bahan yang dibutuhkan tidak cukup!';
                }
            }
            // Ga punya kombinasi mesin 
            else {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';
            }
        }
        // Produksi Saos Tomat
        else if ($product->id == 3) {
            // Ambil kombinasi machine udang kaleng dari team
            $team_machine_combination = $team->machineCombinations->where('id', '102')->first();
            $combination_total = 0;
            if ($team_machine_combination != null) {
                $combination_total = count($team_machine_combination);
            }
            // Lihat team punya kombinasi atau nggak?
            if ($combination_total > 0) {
                //Ambil bahan
                //Ambil Kepala Udang
                $kepala_udang = $team->ingridients->where('id', 11)->first();
                $jumlah_kepala_udang = 0;
                if ($kepala_udang != null) {
                    $jumlah_kepala_udang = $team->ingridients->where('id', 11)->first()->pivot->amount_have;
                }
                //Ambil Tomat
                $tomat = $team->ingridients->where('id', 4)->first();
                $jumlah_tomat = 0;
                if ($tomat != null) {
                    $jumlah_tomat = $team->ingridients->where('id', 4)->first()->pivot->amount_have;
                }
                //Ambil MSG
                $msg = $team->ingridients->where('id', 8)->first();
                $jumlah_msg = 0;
                if ($msg != null) {
                    $jumlah_msg = $team->ingridients->where('id', 8)->first()->pivot->amount_have;
                }
                // Ambil garam
                $garam = $team->ingridients->where('id', 6)->first();
                $jumlah_garam = 0;
                if ($garam != null) {
                    $jumlah_garam = $team->ingridients->where('id', 6)->first()->pivot->amount_have;
                }
                // Ambil gula
                $gula = $team->ingridients->where('id', 7)->first();
                $jumlah_gula = 0;
                if ($gula != null) {
                    $jumlah_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_have;
                }

                //Cek apakah bahan tersedia?
                $bahan_kepala_udang = $banyak_item * 1000; //karena disimpannya dalam satuan gram
                $bahan_msg = $banyak_item * 1; //disimpannya dalam satuan bungkus
                $bahan_garam = $banyak_item * 1; //satuannya dalam bungkus
                $bahan_gula = $banyak_item * 1; //satuannya dalam bungkus
                $bahan_tomat = $banyak_item * 1; //satuannya dalam kg

                //Bahan cukup atau tidak?
                if (
                    $jumlah_kepala_udang >= $bahan_kepala_udang && $jumlah_tomat >= $bahan_tomat
                    && $jumlah_msg >= $bahan_msg && $jumlah_gula >= $bahan_gula && $jumlah_garam >= $bahan_garam
                ) {
                    // Bahan dikuragi

                    // Kurangi kulit udang
                    $amount_use_kepala = $team->ingridients->where('id', 11)->first()->pivot->amount_use;
                    $team->ingridients()->sync([11 => ['amount_have' => $jumlah_kepala_udang - $bahan_kepala_udang, 'amount_use' => $amount_use_kepala + $bahan_kepala_udang]], false);
                    // Kurangi MSG
                    $amount_use_msg = $team->ingridients->where('id', 8)->first()->pivot->amount_use;
                    $team->ingridients()->sync([8 => ['amount_have' => $jumlah_msg - $bahan_msg, 'amount_use' => $amount_use_msg + $bahan_msg]], false);
                    // Kurangi Tomat
                    $amount_use_tomat = $team->ingridients->where('id', 4)->first()->pivot->amount_use;
                    $team->ingridients()->sync([4 => ['amount_have' => $jumlah_tomat - $bahan_tomat, 'amount_use' => $amount_use_tomat + $bahan_tomat]], false);
                    // Kurangi Garam
                    $amount_use_garam = $team->ingridients->where('id', 6)->first()->pivot->amount_use;
                    $team->ingridients()->sync([6 => ['amount_have' => $jumlah_garam - $bahan_garam, 'amount_use' => $amount_use_garam + $bahan_garam]], false);
                    // Kurangi Gula
                    $amount_use_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_use;
                    $team->ingridients()->sync([7 => ['amount_have' => $jumlah_gula - $bahan_gula, 'amount_use' => $amount_use_gula + $bahan_gula]], false);

                    //Produk ditambah
                    $team_product = $team->products->where('id', $product->id)->first();
                    $amount_product = 0;
                    if ($team_product != null) {
                        $amount_product = $team_product->pivot->amount_have;
                    }

                    //Update data
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + (4 * $banyak_item)]]);
                    $team->save();

                    $status = 'success';
                    $msg = 'Produksi berhasil dilakukan!';
                }
                // Ga punya kombinasi mesin 
                else {
                    $status = 'error';
                    $msg = 'Bahan yang dibutuhkan tidak cukup!';
                }
            } else {
                $status = 'error';
                $msg = 'Kombinasi yang dimiliki untuk melakukan produksi ' . $product->name . ' belum sesuai!';
            }
        } else {
            $status = 'error';
            $msg = 'Product yang akan diproduksi tidak valid!';
        }
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
