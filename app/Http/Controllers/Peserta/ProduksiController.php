<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Product;
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
                // Ambil Udang vaname
                $udang_vaname = $team->ingridients->where('id', 1)->first();
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

                $udang_dipakai = '';
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

                $bahan_udang = $banyak_item * 1;
                $bahan_air_mineral = $banyak_item * 1;
                $bahan_garam = $banyak_item * 1;
                $bahan_gula = $banyak_item * 1;

                // Kalau bahan udang cukup
                if ($bahan_udang >= $udang_dipakai && $bahan_air_mineral && $jumlah_air_mineral && $bahan_garam >= $jumlah_garam && $bahan_gula >= $jumlah_gula) {

                    // Kalau punya head pealer
                    $team_head_pealer = $team->machines->where('id', 6)->first();
                    if ($team_head_pealer != null) {
                        //Menghasilkan kepala udang
                        $amount_have = $team->ingridients->where('id', 11)->first()->amount_have;
                        // Tambahkan 100 Kepala udang tiap 1 banyak item
                        $team->ingridients()->sync([11 => ['amount_have' => $amount_have + (100 * $banyak_item)]], false);
                    }
                    // Kalau punya skin pealer
                    $team_skin_pealer = $team->machines->where('id', 7)->first();
                    if ($team_skin_pealer != null) {
                        //Menghasilkan kulit udang
                        $amount_have = $team->ingridients->where('id', 12)->first()->amount_have;
                        // Tambahkan 100 kulit udang tiap 1 banyak item
                        $team->ingridients()->sync([12 => ['amount_have' => $amount_have + (200 * $banyak_item)]], false);
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
                    $team->ingridients()->sync([7 => ['amount_have' => $gula - $bahan_gula, 'amount_use' => $amount_use_gula + $bahan_gula]], false);
                    //Produk ditambah
                    $amount_product = $team->products->where('id', $product->id)->first()->pivot->amount_have;
                    // Tambahkan 4 * banyak item udang kaleng
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + (4 * $banyak_item)]]);
                    $team->waste = $team->waste + (2 * $banyak_item);
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
                $jumlah_kulit_udang = $team->ingridients->where('id', 12)->first()->pivot->amount_have;
                //Ambil NaOH
                $jumlah_naoh = $team->ingridients->where('id', 9)->first()->pivot->amount_have;
                //Ambil HCl
                $jumlah_hcl = $team->ingridients->where('id', 10)->first()->pivot->amount_have;

                //Cek apakah bahan tersedia?
                $bahan_kulit_udang = $banyak_item * 1000;
                $bahan_naoh = $banyak_item * 1;
                $bahan_hcl = $banyak_item * 1;

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
                    $amount_product = $team->products->where('id', $product->id)->first()->pivot->amount_have;

                    //Update data
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + (4 * $banyak_item)]]);
                    $team->waste = $team->waste + (2 * $banyak_item);
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
                $jumlah_kepala_udang = $team->ingridients->where('id', 11)->first()->pivot->amount_have;
                //Ambil Tomat
                $jumlah_tomat = $team->ingridients->where('id', 4)->first()->pivot->amount_have;
                //Ambil MSG
                $jumlah_msg = $team->ingridients->where('id', 8)->first()->pivot->amount_have;
                // Ambil garam
                $garam = $team->ingridients->where('id', 6)->first()->pivot->amount_have;
                // Ambil gula
                $gula = $team->ingridients->where('id', 7)->first()->pivot->amount_have;

                //Cek apakah bahan tersedia?
                $bahan_kepala_udang = $banyak_item * 1000;
                $bahan_msg = $banyak_item * 1;
                $bahan_garam = $banyak_item * 1;
                $bahan_gula = $banyak_item * 1;
                $bahan_tomat = $banyak_item * 1;

                //Bahan cukup atau tidak?
                if (
                    $jumlah_kepala_udang >= $bahan_kepala_udang && $jumlah_tomat >= $bahan_tomat
                    && $jumlah_msg >= $bahan_msg && $gula >= $bahan_gula && $garam >= $bahan_garam
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
                    $team->ingridients()->sync([6 => ['amount_have' => $garam - $bahan_garam, 'amount_use' => $amount_use_garam + $bahan_garam]], false);
                    // Kurangi Gula
                    $amount_use_gula = $team->ingridients->where('id', 7)->first()->pivot->amount_use;
                    $team->ingridients()->sync([7 => ['amount_have' => $gula - $bahan_gula, 'amount_use' => $amount_use_gula + $bahan_gula]], false);

                    //Produk ditambah
                    $amount_product = $team->products->where('id', $product->id)->first()->pivot->amount_have;

                    //Update data
                    $team->products()->sync([$product->id => ['amount_have' => $amount_product + (4 * $banyak_item)]]);
                    $team->waste = $team->waste + (2 * $banyak_item);
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
