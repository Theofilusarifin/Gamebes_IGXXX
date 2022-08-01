<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Product;
use App\SeasonNow;
use App\Team;
use App\Transport;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        $products = Product::all();
        $transports = Transport::all();
        return view('peserta.marketing.index', compact('teams', 'products', 'transports'));
    }

    public function sell(Request $request)
    {
        // Ambil data dari ajax
        $team = Team::find($request['team_id']);
        $product = Product::find($request['product_id']);
        $transport = Transport::find($request['transport_id']);
        $banyak_item = $request['banyak_item'];

        // Status dan message untuk respond
        $status = '';
        $msg = '';

        // Kalau team tidak ada
        if (!isset($team)) {
            $status = 'error';
            $msg = 'Pilih team terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Kalau transport yang digunakan tidak ada
        if (!isset($transport)) {
            $status = 'error';
            $msg = 'Pilih transport terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Kalau product yang dipilih kosong
        if (!isset($product)) {
            $status = 'error';
            $msg = 'Pilih product terlebih dahulu!';

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

        if (!isset($banyak_item)) {
            $status = 'error';
            $msg = 'Isi banyak barang yang ingin dijual!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila banyak item melebihi kapasitas transport
        if ($banyak_item > $team_transport->capacity) {
            $status = 'error';
            $msg = 'Banyak product yang ingin dijual melebihi kapasitas transportasi!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }
        
        // Ambil team product
        $team_product = $team->products->where('id', $product->id)->first();
        // Team punya productnya ga?
        if ($team_product == null) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product ' . $product->name . '!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Cek apakah produk yang dipilih masih dimiliki oleh team?
        $banyak_product_yang_dimiliki = $team_product->pivot->amount_have;
        if ($banyak_product_yang_dimiliki == 0) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' tidak memiliki product ' . $product->name . '!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila produk yang dimiliki lebih sedikit daripada yang ingin dijual
        if ($banyak_item > $banyak_product_yang_dimiliki) {
            $status = 'error';
            $msg = 'Team ' . $team->name . ' hanya meliki ' . $product->name . ' sebanyak ' . $banyak_product_yang_dimiliki . ' buah!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Tentuin harga jual product di musim sekarang
        $harga_jual = $team_product->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price; //Ini problem

        // Harga total
        $harga_total = $banyak_item * $harga_jual;

        // Kasik uang hasil penjualan ke team
        $team->tc = $team->tc + $harga_total;
        $team->total_income = $team->total_income + $harga_total;
        $team->save();
        // Kurangi amount product yang dijual
        $amount_have_new = $banyak_product_yang_dimiliki - $banyak_item;
        $amount_sold_new = $team_product->pivot->amount_sold + $banyak_item;
        $total = $team_product->pivot->total + $harga_total;
        $team->products()->sync([$product->id => [
            'amount_have' => $amount_have_new,
            'amount_sold' => $amount_sold_new,
            'total' => $total
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
