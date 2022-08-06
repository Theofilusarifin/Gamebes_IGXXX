<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\Season;
use App\SeasonNow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescriptionController extends Controller
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

        $machines = Machine::all();
        return view('peserta.deskripsi.index', compact(
            'machines',
        ));
    }
}
