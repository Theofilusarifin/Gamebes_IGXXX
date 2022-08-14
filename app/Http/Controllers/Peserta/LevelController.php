<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Product;
use App\Season;
use App\SeasonNow;
use App\Territory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LevelController extends Controller
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

        //Deklarasi
        $team = Auth::user()->team;

        if ($team->level < 3) {
            $level_id = $team->level + 1;
        } else {
            $level_id = $team->level;
        }

        $team_level = DB::table('team_level')->where('team_id', $team->id)->where('level_id', $level_id)->first();
        return view('peserta.level.index', compact('team', 'team_level'));
    }

    public function updateSyarat()
    {
        // Inisiasi Variabel
        $status = '';
        $msg = '';

        $team_machine_effectivity = null;
        $team_machine_higenity = null;
        $persentase_limbah = null;

        //Deklarasi teamnya
        $team = Auth::user()->team;

        if ($team->level >= 3) {
            $level_id = $team->level;
            $team_level = $team->levels->where('id', $level_id)->first();
            return response()->json(array(
                'team_level' => $team_level,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $level_id = $team->level + 1;
        //Kalau team level tidak null ambil detail team level
        $team_level = $team->levels->where('id', $level_id)->first();

        //Ambil Kombinasi Team yang sedang dipakai
        $team_machine_combination = $team->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();

        if ($team_machine_combination != null) {
            $team_machine_effectivity = $team_machine_combination->effectivity;
            $team_machine_higenity = $team_machine_combination->higenity;
        }

        //HITUNG PERSENTASE LIMBAH
        //Ambil product team udang kaleng
        $team_udang_kaleng = $team->products->where("id", 1)->first();

        if ($team_udang_kaleng != null) {
            // Ambil banyaknya udang kaleng yang sudah diproduksi oleh team
            $total_udang_kaleng = $team_udang_kaleng->pivot->amount_have + $team_udang_kaleng->pivot->amount_sold;

            // Ambil total waste
            $total_limbah = $team->waste;
            $persentase_limbah = $total_limbah / $total_udang_kaleng;
        }

        //Pengecekan
        //     Efektivitas & Higenity 
        //Level 1 --> 40++ & 40++, TC >= 1000, Persentase Limbah <= 20%
        //Level 2 --> 60++ & 60++, TC >= 1500, Persentase Limbah <= 15%
        //Level 3 --> 70++ & 80++, TC >= 2000, Persentase Limbah <= 10% 

        //Pengecekan Level 1
        if ($team->level == 0) {

            // CHECK SYARAT 1 -> EFECTIVITY
            if ($team_machine_effectivity != null) {
                if ($team_machine_effectivity >= 40) {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
            }

            // CHECK SYARAT 2 -> HIGENITY
            if ($team_machine_higenity != null) {
                if ($team_machine_higenity >= 40) {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
            }

            // CHECK SYARAT 3 -> TIGGIE COIN

            if ($team->tc >= 1000) {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 1]], false);
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 0]], false);
            }

            // CHECK SYARAT 4 -> LIMBAH
            if ($persentase_limbah != null) {
                if ($persentase_limbah <= 0.20) {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
            }
        }

        // Pengecekan Level 2
        else if ($team->level == 1) {

            // CHECK SYARAT 1 -> EFECTIVITY
            if ($team_machine_effectivity != null) {
                if ($team_machine_effectivity >= 60) {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
            }

            // CHECK SYARAT 2 -> HIGENITY
            if ($team_machine_higenity != null) {
                if ($team_machine_higenity >= 60) {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
            }

            // CHECK SYARAT 3 -> TIGGIE COIN
            if ($team->tc >= 1500) {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 1]], false);
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 0]], false);
            }

            // CHECK SYARAT 4 -> LIMBAH
            if (
                $persentase_limbah != null
            ) {
                if ($persentase_limbah <= 0.15) {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
            }
        }

        //Pengecekan Level 3
        else if ($team->level == 2) {

            // CHECK SYARAT 1 -> EFECTIVITY
            if ($team_machine_effectivity != null) {
                if ($team_machine_effectivity >= 70) {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_1' => 0]], false);
            }

            // CHECK SYARAT 2 -> HIGENITY
            if ($team_machine_higenity != null) {
                if ($team_machine_higenity >= 80) {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_2' => 0]], false);
            }

            // CHECK SYARAT 3 -> TIGGIE COIN
            if ($team->tc >= 2000) {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 1]], false);
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_3' => 0]], false);
            }

            // CHECK SYARAT 4 -> LIMBAH
            if (
                $persentase_limbah != null
            ) {
                if ($persentase_limbah <= 0.10) {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 1]], false);
                } else {
                    $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
                }
            } else {
                $team->levels()->sync([$team_level->id => ['syarat_4' => 0]], false);
            }
        }

        //Perbaruhi Variabel Team Level
        $team_level = DB::table('team_level')->where('team_id', $team->id)->where('level_id', $level_id)->first();
        // dd($team_level);
        $status = 'success';
        $msg = 'Syarat Berhasil Diperbaharui';

        return response()->json(array(
            'team_level' => $team_level,
            'team' => $team,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function upgradeLevel()
    {
        // Inisiasi Variabel
        $status = '';
        $msg = '';

        //Deklarasi teamnya
        $team = Auth::user()->team;
        if ($team->level < 3) {
            $team_level = $team->levels->where('id', $team->level + 1)->first();
        } else {
            $team_level = $team->levels->where('id', $team->level)->first();
            return response()->json(array(
                'team_level' => $team_level,
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        $total_syarat_terpenuhi = $team_level->pivot->syarat_1 + $team_level->pivot->syarat_2 + $team_level->pivot->syarat_3  + $team_level->pivot->syarat_4;

        if ($total_syarat_terpenuhi == 4) {
            $team->level = $team->level + 1;
            $team->save();

            $team->levels()->attach($team->level + 1, [
                'syarat_1' => 0,
                'syarat_2' => 0,
                'syarat_3' => 0,
                'syarat_4' => 0,
            ]);

            // Update Team Company di territory team
            $team_company = Territory::where('url_company', $team->id)->first();
            $team_company->company_level = $team_company->company_level + 1;
            $team_company->save();

            $status = 'success';
            $msg = 'Selamat! Team anda telah berhasil mencapai level ' . $team->level . '!';
        } else {
            $status = 'error';
            $msg = 'Syarat belum terpenuhi untuk mencapai level ' . ($team->level + 1) . '!';
        }


        return response()->json(array(
            'team_level' => $team_level,
            'team' => $team,
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
