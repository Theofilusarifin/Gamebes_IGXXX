<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Product;
use App\Season;
use App\SeasonNow;
use App\Team;
use App\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
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
        $now = date('Y-m-d H:i:s');
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

        // Deklarasi
        $team = Auth::user()->team;

        // Ambil semua transport yang dimiliki team
        $team_transports = $team->transports->all();
        return view('peserta.marketing.index', compact('team_transports'));
    }

    public function sell(Request $request)
    {
        // Ambil data dari ajax
        $team = Auth::user()->team;

        // Cek apakah requestnya diisi atau tidak ?
        $banyak_item_1 = ($request['banyak_item_1'] != null) ? (int)$request['banyak_item_1'] : 0;
        $banyak_item_2 = ($request['banyak_item_2'] != null) ? (int)$request['banyak_item_2'] : 0;
        $banyak_item_3 = ($request['banyak_item_3'] != null) ? (int)$request['banyak_item_3'] : 0;

        // Cek Transport 
        $transport = Transport::find($request['transport_id']);

        // Status dan message untuk respond
        $status = '';
        $msg = '';

        //Kalau transport yang digunakan tidak ada
        if (!isset($transport)) {
            $status = 'error';
            $msg = 'Pilih transport terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Ambil team_transport yang dipilih
        $team_transport = $team->transports->where('id', $transport->id)->first();

        // Kalau team tidak memiliki transportasi yang dipilih
        if ($team_transport == null) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki transport ' . $transport->name . '!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // if (!isset($banyak_item)) {
        //     $status = 'error';
        //     $msg = 'Isi banyak barang yang ingin dijual!';

        //     return response()->json(array(
        //         'status' => $status,
        //         'msg' => $msg,
        //     ), 200);
        // }
        $total_item = $banyak_item_1 + $banyak_item_2 + $banyak_item_3;

        // Apabila banyak item melebihi kapasitas transport
        if ($total_item > $team_transport->capacity) {
            $status = 'error';
            $msg = 'Banyak product yang ingin dijual melebihi kapasitas transportasi!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Ambil team product
        // Product Udang Kaleng 
        $team_product_kaleng = $team->products->where('id', 1)->first();
        // Product Kitosan
        $team_product_kitosan = $team->products->where('id', 2)->first();
        // Product Saus Tomat
        $team_product_saus = $team->products->where('id', 3)->first();

        // Team punya productnya ga?
        if ($team_product_kaleng == null) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Udang Kaleng!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        if ($team_product_kitosan == null) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Kitosan!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        if ($team_product_saus == null) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Saus Tomat!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Cek apakah produk yang dipilih masih dimiliki oleh team?
        $banyak_kaleng_yang_dimiliki = $team_product_kaleng->pivot->amount_have;
        if ($banyak_kaleng_yang_dimiliki == 0) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Udang Kaleng!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        $banyak_kitosan_yang_dimiliki = $team_product_kitosan->pivot->amount_have;
        if ($banyak_kitosan_yang_dimiliki == 0) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Kitosan!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        $banyak_saus_yang_dimiliki = $team_product_saus->pivot->amount_have;
        if ($banyak_saus_yang_dimiliki == 0) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product Saus Tomat!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila produk yang dimiliki lebih sedikit daripada yang ingin dijual
        if ($banyak_item_1 > $banyak_kaleng_yang_dimiliki) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' hanya meliki Udang Kaleng sebanyak ' . $banyak_kaleng_yang_dimiliki . ' buah!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        if ($banyak_item_2 > $banyak_kitosan_yang_dimiliki) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' hanya meliki Kitosan sebanyak ' . $banyak_kitosan_yang_dimiliki . ' buah!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        if ($banyak_item_3 > $banyak_saus_yang_dimiliki) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' hanya meliki Saus Tomat sebanyak ' . $banyak_saus_yang_dimiliki . ' buah!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Tentuin harga jual product di musim sekarang
        $harga_jual_kaleng = $team_product_kaleng->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;
        $harga_jual_kitosan = $team_product_kitosan->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;
        $harga_jual_saus = $team_product_saus->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;

        // Harga total
        $total_jual_kaleng = $banyak_item_1 * $harga_jual_kaleng;
        $total_jual_kitosan = $banyak_item_2 * $harga_jual_kitosan;
        $total_jual_saus = $banyak_item_3 * $harga_jual_saus;

        $harga_total = $total_jual_kaleng + $total_jual_kitosan + $total_jual_saus;

        // Kasik uang hasil penjualan ke team
        $team->tc = $team->tc + $harga_total;
        $team->total_income = $team->total_income + $harga_total;
        $team->save();

        // Kurangi amount product yang dijual
        $amount_have_new_kaleng = $banyak_kaleng_yang_dimiliki - $banyak_item_1;
        $amount_have_new_kitosan = $banyak_kitosan_yang_dimiliki - $banyak_item_2;
        $amount_have_new_saus = $banyak_saus_yang_dimiliki - $banyak_item_3;

        // Tambahkan amount sold product yang dijual
        $amount_sold_new_kaleng = $team_product_kaleng->pivot->amount_sold + $banyak_item_1;
        $amount_sold_new_kitosan = $team_product_kitosan->pivot->amount_sold + $banyak_item_2;
        $amount_sold_new_saus = $team_product_saus->pivot->amount_sold + $banyak_item_3;

        // Update databasenya
        $total_kaleng = $team_product_kaleng->pivot->total + $harga_total;
        $team->products()->sync([1 => [
            'amount_have' => $amount_have_new_kaleng,
            'amount_sold' => $amount_sold_new_kaleng,
            'total' => $total_kaleng
        ]], false);

        $total_kitosan = $team_product_kitosan->pivot->total + $harga_total;
        $team->products()->sync([2 => [
            'amount_have' => $amount_have_new_kitosan,
            'amount_sold' => $amount_sold_new_kitosan,
            'total' => $total_kitosan
        ]], false);

        $total_saus = $team_product_saus->pivot->total + $harga_total;
        $team->products()->sync([3 => [
            'amount_have' => $amount_have_new_saus,
            'amount_sold' => $amount_sold_new_saus,
            'total' => $total_saus
        ]], false);

        // Tambahkan penggunaan transport
        $team->transports()->sync([$transport->id => [
            'use_num' => $team_transport->pivot->use_num + 1
        ]], false);

        $status = 'success';
        $msg = 'Penjualan berhasil dilakukan!';

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
