<?php

namespace App\Http\Controllers\Penpos;

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
        return view('penpos.marketing.index', compact('teams', 'products', 'transports'));
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

        // Kalau team ada
        if (isset($team)) {
            if (isset($transport)) {
                if (isset($product)) {
                    $team_transport = $team->transports->where('id', $transport->id)->first();
                    if ($team_transport != null) {
                        if (isset($banyak_item)) {
                            if ($banyak_item <= $team_transport->capacity) {
                                // Ambil team product
                                $team_product = $team->products->where('id', $product->id)->first();
                                // Team punya productnya ga?
                                if ($team_product != null) {
                                    $banyak_product_yang_dimiliki = $team_product->pivot->amount_have;
                                    // Productnya udah habis terjual semua ga?
                                    if ($banyak_product_yang_dimiliki != 0) {
                                        // Punya product banyaknya segitu ga?
                                        if ($banyak_item <= $banyak_product_yang_dimiliki) {
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
                                        } else {
                                            $status = 'error';
                                            $msg = 'Team ' . $team->name . ' hanya meliki ' . $product->name . ' sebanyak ' . $banyak_product_yang_dimiliki . ' buah!';
                                        }
                                    } else {
                                        $status = 'error';
                                        $msg = 'Team ' . $team->name . ' tidak memiliki product ' . $product->name . '!';
                                    }
                                } else {
                                    $status = 'error';
                                    $msg = 'Team ' . $team->name . ' tidak memiliki product ' . $product->name . '!';
                                }
                            } else {
                                $status = 'error';
                                $msg = 'Banyak product yang ingin dijual melebihi kapasitas transportasi!';
                            }
                        } else {
                            $status = 'error';
                            $msg = 'Isi banyak barang yang ingin dijual!';
                        }
                    } else {
                        $status = 'error';
                        $msg = 'Team ' . $team->name . ' tidak memiliki transport ' . $transport->name . '!';
                    }
                } else {
                    $status = 'error';
                    $msg = 'Pilih product terlebih dahulu!';
                }
            } else {
                $status = 'error';
                $msg = 'Pilih transport terlebih dahulu!';
            }
        } else {
            $status = 'error';
            $msg = 'Pilih team terlebih dahulu!';
        }
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
