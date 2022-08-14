<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Product;
use App\Season;
use App\SeasonNow;
use App\Team;
use App\TeamTransport;
use App\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Deklarasi
        $team = Auth::user()->team;
        // Ambil semua transport yang dimiliki team
        $team_transports = TeamTransport::where('team_id', $team->id)->get();
        return view('peserta.marketing.index', compact('team_transports', 'team'));
    }

    public function cooldown(Request $request)
    {
        $team_transport = TeamTransport::find($request['transport_id']);
        $status = 'success';

        return response()->json(array(
            'team_transport' => $team_transport,
            'status' => $status,
        ), 200);
    }

    public function nullCooldown(Request $request)
    {
        $team_transport = TeamTransport::find($request['team_transport_id']);

        if ($team_transport == null) {
            $status = 'error';

            return response()->json(array(
                'team_transport' => $team_transport,
                'status' => $status,
            ), 200);
        }

        DB::statement("UPDATE `transport_team` SET cooldown_marketing = NULL WHERE id = $team_transport->id");
        $status = 'success';

        return response()->json(array(
            'team_transport' => $team_transport,
            'status' => $status,
        ), 200);
    }

    public function sell(Request $request)
    {
        // Ambil data dari ajax
        $team = Auth::user()->team;

        // Cek apakah requestnya diisi atau tidak ?
        $banyak_item_1 = ($request['banyak_item_1'] != null) ? (int)$request['banyak_item_1'] : 0;
        $banyak_item_2 = ($request['banyak_item_2'] != null) ? (int)$request['banyak_item_2'] : 0;
        $banyak_item_3 = ($request['banyak_item_3'] != null) ? (int)$request['banyak_item_3'] : 0;

        // Ambil Team Transport yang dipilih 
        $team_transport = TeamTransport::find($request['transport_id']);

        // Cek Transport 
        $transport = Transport::find($team_transport->transport_id);

        // Status dan message untuk respond
        $status = '';
        $msg = '';

        $harga_jual_kaleng = 0;
        $harga_jual_kitosan = 0;
        $harga_jual_saus = 0;

        //Kalau transport yang digunakan tidak ada
        if (!isset($team_transport)) {
            $status = 'error';
            $msg = 'Pilih transport terlebih dahulu!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $total_item = $banyak_item_1 + $banyak_item_2 + $banyak_item_3;

        // Apabila banyak item melebihi kapasitas transport
        if ($total_item > $transport->capacity) {
            $status = 'error';
            $msg = 'Banyak product yang ingin dijual melebihi kapasitas transportasi!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Cek apakah ada cooldown?
        if ($team_transport->cooldown_marketing != null) {
            $status = 'error';
            $msg = $transport->name. ' masih dalam cooldown!';

            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Ambil team product
        // Product Udang Kaleng 
        $team_product_kaleng = $team->products->where('id', 1)->first();

        // Team punya productnya ga?
        if ($team_product_kaleng != null) {
            // Ambil amount have untuk pengecekan
            $banyak_kaleng_yang_dimiliki = $team_product_kaleng->pivot->amount_have;
            // Apabila produk yang dimiliki lebih sedikit daripada yang ingin dijual
            if ($banyak_item_1 > $banyak_kaleng_yang_dimiliki) {
                $status = 'error';
                $msg = 'Team ' . $team->name . ' hanya memiliki Udang Kaleng sebanyak ' . $banyak_kaleng_yang_dimiliki . ' buah!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }
            // Tentuin harga jual product di musim sekarang
            $harga_jual_kaleng = $team_product_kaleng->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;

            // Harga total
            $total_jual_kaleng = $banyak_item_1 * $harga_jual_kaleng;

            // Kasik uang hasil penjualan ke team
            $team->tc = $team->tc + $total_jual_kaleng;
            $team->total_income = $team->total_income + $total_jual_kaleng;
            $team->save();

            // Kurangi amount product yang dijual
            $amount_have_new_kaleng = $banyak_kaleng_yang_dimiliki - $banyak_item_1;
            // Tambahkan amount sold product yang dijual
            $amount_sold_new_kaleng = $team_product_kaleng->pivot->amount_sold + $banyak_item_1;

            // Update databasenya
            $total_kaleng = $team_product_kaleng->pivot->total + $total_jual_kaleng;
            $team->products()->sync([1 => [
                'amount_have' => $amount_have_new_kaleng,
                'amount_sold' => $amount_sold_new_kaleng,
                'total' => $total_kaleng
            ]], false);
        }

        // Product Kitosan
        $team_product_kitosan = $team->products->where('id', 2)->first();
        // Team punya productnya ga?
        if ($team_product_kitosan != null) {
            // Ambil amount have untuk pengecekan
            $banyak_kitosan_yang_dimiliki = $team_product_kitosan->pivot->amount_have;
            // Apabila produk yang dimiliki lebih sedikit daripada yang ingin dijual
            if ($banyak_item_2 > $banyak_kitosan_yang_dimiliki) {
                $status = 'error';
                $msg = 'Team ' . $team->name . ' hanya memiliki Kitosan sebanyak ' . $banyak_kitosan_yang_dimiliki . ' buah!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }
            // Tentuin harga jual product di musim sekarang
            $harga_jual_kitosan = $team_product_kitosan->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;

            // Harga total
            $total_jual_kitosan = $banyak_item_2 * $harga_jual_kitosan;

            // Kasik uang hasil penjualan ke team
            $team->tc = $team->tc + $total_jual_kitosan;
            $team->total_income = $team->total_income + $total_jual_kitosan;
            $team->save();

            // Kurangi amount product yang dijual
            $amount_have_new_kitosan = $banyak_kitosan_yang_dimiliki - $banyak_item_2;
            // Tambahkan amount sold product yang dijual
            $amount_sold_new_kitosan = $team_product_kitosan->pivot->amount_sold + $banyak_item_2;

            // Update databasenya
            $total_kitosan = $team_product_kitosan->pivot->total + $total_jual_kitosan;
            $team->products()->sync([2 => [
                'amount_have' => $amount_have_new_kitosan,
                'amount_sold' => $amount_sold_new_kitosan,
                'total' => $total_kitosan
            ]], false);
        }
        // Product Saus Tomat
        $team_product_saus = $team->products->where('id', 3)->first();

        // Team punya productnya ga?
        if ($team_product_saus != null) {
            // Ambil amount have untuk pengecekan
            $banyak_saus_yang_dimiliki = $team_product_saus->pivot->amount_have;
            // Apabila produk yang dimiliki lebih sedikit daripada yang ingin dijual
            if ($banyak_item_3 > $banyak_saus_yang_dimiliki) {
                $status = 'error';
                $msg = 'Team ' . $team->name . ' hanya memiliki Saus Tomat sebanyak ' . $banyak_saus_yang_dimiliki . ' buah!';

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                ), 200);
            }
            // Tentuin harga jual product di musim sekarang
            $harga_jual_saus = $team_product_saus->seasons->where('id', SeasonNow::first()->id)->first()->pivot->price;

            // Harga total
            $total_jual_saus = $banyak_item_3 * $harga_jual_saus;

            // Kasik uang hasil penjualan ke team
            $team->tc = $team->tc + $total_jual_saus;
            $team->total_income = $team->total_income + $total_jual_saus;
            $team->save();

            // Kurangi amount product yang dijual
            $amount_have_new_saus = $banyak_saus_yang_dimiliki - $banyak_item_3;
            // Tambahkan amount sold product yang dijual
            $amount_sold_new_saus = $team_product_saus->pivot->amount_sold + $banyak_item_3;

            // Update databasenya
            $total_saus = $team_product_saus->pivot->total + $total_jual_saus;
            $team->products()->sync([3 => [
                'amount_have' => $amount_have_new_saus,
                'amount_sold' => $amount_sold_new_saus,
                'total' => $total_saus
            ]], false);
        }

        // SET COOLDOWN
        // Waktu di Surabaya sekarang
        $now = DB::select(DB::raw("SELECT CURRENT_TIMESTAMP() as waktu"))[0]->waktu;

        // Tambah 1 menit waktu di surabaya sekarang
        $cooldown_selesai = date(
            'Y-m-d H:i:s',
            strtotime('+1 minutes', strtotime($now))
        );

        // Set waktu untuk cooldownya
        DB::statement("UPDATE `transport_team` SET cooldown_marketing = '$cooldown_selesai' WHERE id = $team_transport->id");

        // Tambahkan penggunaan transport
        $team_transport->use_num = $team_transport->use_num + 1;
        $team_transport->save();

        $status = 'success';
        $msg = 'Penjualan berhasil dilakukan!';

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
