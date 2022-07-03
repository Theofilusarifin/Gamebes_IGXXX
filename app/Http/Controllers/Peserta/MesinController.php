<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use App\SeasonNow;
use App\TeamMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MesinController extends Controller
{
    public function index()
    {
        //Declare
        $teams = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machines = TeamMachine::where('team_id', $teams->id)->where('season_sell', null)->get();

        // reset selected
        foreach ($team_machines as $team_machine) {
            $team_machine->selected = 0;
            $team_machine->save();
        }

        // Ambil machine combination sebagai default dari combobox
        //101 --> saus tomat, 102 --> kitosan
        $machine_combination_udang = $teams->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
        $machine_combination_saus = $teams->machineCombinations->where("id", "101")->first();
        $machine_combination_kitosan = $teams->machineCombinations->where("id", "102")->first();

        //Deklarasi untuk nampung efektivitas dari kombinasi yang dipakai
        $machine_udang_tersimpan = "";
        $machine_saus_tersimpan = "";
        $machine_kitosan_tersimpan = "";

        if ($machine_combination_udang != null) {
            $machine_udang_tersimpan = $machine_combination_udang->machines->sortBy('pivot.order');
        }
        if ($machine_combination_saus != null) {
            $machine_saus_tersimpan = $machine_combination_saus->machines->sortBy('pivot.order');
        }
        if ($machine_combination_kitosan != null) {
            $machine_kitosan_tersimpan = $machine_combination_kitosan->machines->sortBy('pivot.order');
        }

        return view('peserta.mesin.index', compact(
            'teams',
            'team_machines',
            'machine_combination_udang',
            'machine_combination_saus',
            'machine_combination_kitosan',
            'machine_udang_tersimpan',
            'machine_saus_tersimpan',
            'machine_kitosan_tersimpan'
        ));
    }

    public function getAvailableMachine()
    {
        // Ambil Team
        $team = Auth::user()->team;
        // Ambil team machine yang not selected dan belum dijual 
        $available_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();

        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($available_machines as $available_machine) {
            $machine = Machine::where('id', $available_machine->machine_id)->first();
            $available_machines[$index]->machine = $machine;
            $index++;
        }

        $status = 'success';

        return response()->json(array(
            'available_machines' => $available_machines,
            'status' => $status,
        ), 200);
    }

    public function setMachine(Request $request)
    {
        // Ambil Team
        $team = Auth::user()->team;

        // Ambil team machine untuk diubah selectednya
        $team_machine = $team->teamMachines->where('id', $request['team_machine_id'])->first();

        //Ubah selected
        $team_machine->selected = 1;
        $team_machine->save();

        // Ambil team machine yang not selected dan belum dijual 
        $available_machines = TeamMachine::where('team_id', $team->id)->where('selected', 0)->where('season_sell', null)->get();
        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($available_machines as $available_machine) {
            $machine = Machine::where('id', $available_machine->machine_id)->first();
            $available_machines[$index]->machine = $machine;
            $index++;
        }
        $status = 'success';

        return response()->json(array(
            'available_machines' => $available_machines,
            'status' => $status,
        ), 200);
    }

    public function saveMachineTambahan(Request $request)
    {
        //Declare
        $team = Auth::user()->team;
        // Ambil team machine untuk diubah selectednya
        $team_machines = TeamMachine::where('team_id', $team->id)->get();

        // reset selected
        foreach ($team_machines as $team_machine) {
            $team_machine->selected = 0;
            $team_machine->save();
        }

        //Tidak cukup uang
        if ($team->tc < 5) {
            // kurang sesuai tc 
            $team->tc = 0;
            $team->total_spend = $team->total_spend + $team->tc;
            $status = "error";
            $msg = "Tiggie coin anda tidak cukup untuk melakukan penyusunan mesin!";
            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila cukup tc kurangi 5
        $team->tc = $team->tc - 5;
        $team->total_spend = $team->total_spend + 5;

        // total mesin assembly + 1
        $team->machine_assembly = $team->machine_assembly + 1;
        $team->save();

        // Ambil tipenya
        $tipe = $request['tipe'];
        if ($tipe == "ac") {
            //Ambil mesin ac yaitu id 4
            $all_team_mesins = $team->teamMachines->where('machine_id', 4);
            foreach ($all_team_mesins as $team_mesinK) {
                $team_mesinK->is_used = 0;
                $team_mesinK->save();
            }
        } else if ($tipe == "filter") {
            //Ambil mesin filter yaitu id 2
            $all_team_mesins = $team->teamMachines->where('machine_id', 2);
            foreach ($all_team_mesins as $team_mesinK) {
                $team_mesinK->is_used = 0;
                $team_mesinK->save();
            }
        }

        $team_machine_tambahan = $request['mesin']; //Kemungkinan teamMachine_id
        $tm = TeamMachine::find($team_machine_tambahan); //Cari teamMachine yang punya id itu
        $machine_tambahan = Machine::find($tm->machine_id); //Cari mesin yang machine idnya sama dg teamMachine
        //Cek kalau yang didapat itu selain 2 dan 4 berarti return error
        if ($machine_tambahan->id != 2 && $machine_tambahan->id != 4) {
            $status = "error";
            $msg = "Kombinasi yang dimasukkan belum tepat! Kombinasi tidak akan disimpan";
            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        //Ubah is_used mesin jadi 1
        $team_machine = $team->teamMachines->where('machine_id', $team_machine_tambahan)->first();
        $team_machine->is_used = 1;
        $team_machine->save();

        $status = 'success';
        $msg = 'Kombinasi yang dimasukkan sudah benar! Kombinasi akan disimpan.';
        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }

    public function saveMachine(Request $request)
    {
        //Declare
        $team = Auth::user()->team;
        // Ambil team machine untuk diubah selectednya
        $team_machines = TeamMachine::where('team_id', $team->id)->get();

        // reset selected
        foreach ($team_machines as $team_machine) {
            $team_machine->selected = 0;
            $team_machine->save();
        }

        //Tidak cukup uang
        if ($team->tc < 5) {
            // kurang sesuai tc 
            $team->tc = 0;
            $team->total_spend = $team->total_spend + $team->tc;
            $status = "error";
            $msg = "Tiggie coin anda tidak cukup untuk melakukan penyusunan mesin!";
            return response()->json(array(
                'status' => $status,
                'msg' => $msg,
            ), 200);
        }

        // Apabila cukup tc kurangi 5 
        $team->tc = $team->tc - 5;
        $team->total_spend = $team->total_spend + 5;

        // total mesin assembly + 1
        $team->machine_assembly = $team->machine_assembly + 1;
        $team->save();

        // Ambil tipenya
        $tipe = $request['tipe'];

        // Menghapus kombinasi lama untuk diganti kombinasi baru
        if ($tipe == "udang") {
            //101 --> saus tomat, 102 --> kitosan
            //Ambil semua mesin kecuali id 11,12 dan 15,16,17, 2, 4
            $all_team_mesins = $team->teamMachines->where('machine_id', '<', 15)->where('machine_id', '!=', 11)->where('machine_id', '!=', 12)->where('machine_id', '!=', 2)->where('machine_id', '!=', 4);
            foreach ($all_team_mesins as $team_mesinK) {
                $team_mesinK->is_used = 0;
                $team_mesinK->save();
            }
            DB::table('team_machine_combination')
                ->where('machine_combination_id', "!=", 101)
                ->where('machine_combination_id', "!=", 102)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($tipe == "saus") {
            //Ambil semua mesin dengan id 11 dan 12 
            $all_team_mesins = $team->teamMachines->where('machine_id', 11)->orWhere('machine_id', 12);
            foreach ($all_team_mesins as $team_mesinK) {
                $team_mesinK->is_used = 0;
                $team_mesinK->save();
            }
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 101)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($tipe == "kitosan") {
            //Ambil semua mesin dengan id 15 16 17
            $all_team_mesins = $team->teamMachines->where('machine_id', '>=', 15);
            foreach ($all_team_mesins as $team_mesinK) {
                $team_mesinK->is_used = 0;
                $team_mesinK->save();
            }
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 102)
                ->where('team_id', $team->id)
                ->delete();
        }

        $status = '';
        $msg = '';

        // Ambil susunan mesin dari AJAX
        $susunan_mesin = $request['susunan_mesin']; //array yang berisikan team_machine_id [1,2,3,4,null,null,null,null,null,...]

        // Define anyak mesin berapa (buang yang isinya null menggunakan array filter)
        $banyak_machine = count(array_filter($susunan_mesin));

        if ($banyak_machine >= 4) {
            // Masukan order dari tiap mesin
            $orders = [];

            for ($i = 0; $i < $banyak_machine; $i++) {
                // dd($susunan_mesin[$i]);
                // Cari machine_idnya dulu pakai team_machine_id
                $tm = TeamMachine::find($susunan_mesin[$i]);
                // Masukkan machine ke dalam order
                $orders[$i + 1] = Machine::find($tm->machine_id);
            }

            // Dapatkan semua kombinasi dari mesin yang berada pada order yang disusun
            $combinations = [];
            for ($i = 1; $i <= $banyak_machine; $i++) {
                $all_combinations = $orders[$i]->machineCombinations()->wherePivot('order', $i)->get();
                //dd($all_combinations);
                $combination_id = [];
                foreach ($all_combinations as $combination) {
                    $combination_id[] = $combination->id;
                }
                $combinations[] = $combination_id;
            }
            // dd($combinations);
            $combination_found = [];
            if ($banyak_machine > 1) {
                // Lakukan intersect untuk mengetahui apakah ada kombinasi yang cocok
                $combination_found = array_intersect(...$combinations);
            }
            // dd($combination_found);
            // Apabila terdapat persis satu kombinasi yang cocok maka foundnya true
            $found = (count($combination_found) >= 1) ? true : false;
            // Kombinasi ada
            if ($found) {
                // Hapus kombinasi kecuali kombinasi kitosan  dan saus tomat

                // Ambil Kombinasi
                if (count($combination_found) > 1) {
                    //Ini set di team_machine_combination
                    $combination = MachineCombination::find($combination_found[0]);
                    $team->machineCombinations()->attach($combination->id);
                } else {
                    //Ini set di team_machine_combination
                    $combination = MachineCombination::find($combination_found);
                    $team->machineCombinations()->attach($combination[0]->id);
                }
                // Update tambahkan machine combination
                $team->save();

                //Kalau berhasil, diubah is_usednya jadi 1
                for ($i = 0; $i < $banyak_machine; $i++) {
                    $timMesin = TeamMachine::find($susunan_mesin[$i]);
                    $timMesin->is_used = 1;
                    $timMesin->save();
                }
                $status = 'success';
                $msg = 'Kombinasi yang dimasukkan sudah benar! Kombinasi akan disimpan.';
            } else {
                $status = 'error';
                $msg = 'Kombinasi yang dimasukkan belum tepat! Kombinasi tidak akan disimpan.';
                //Semisal team sudah pernah benar kemudian coba kombinasi baru dan salah maka kombinasi lama akan hilang.
            }
        } else {
            $status = 'error';
            $msg = 'Kombinasi yang dimasukkan belum tepat! Kombinasi tidak akan disimpan.';
        }


        //Deklarasi untuk nampung efektivitas dari kombinasi yang dipakai
        $machine_combination_udang = $team->machineCombinations->where("id", "!=", "101")->where("id", "!=", "102")->first();
        $machine_combination_saus = $team->machineCombinations->where("id", "101")->first();
        $machine_combination_kitosan = $team->machineCombinations->where("id", "102")->first();

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
            'machine_combination_saus' => $machine_combination_saus,
            'machine_combination_kitosan' => $machine_combination_kitosan,
            'machine_combination_udang' => $machine_combination_udang,
        ), 200);
    }

    public function sellMachine(Request $request)
    {
        $status = '';
        $msg = '';

        // Define Variable
        $team = Auth::user()->team;
        $team_machine = TeamMachine::find($request['team_machine_id']); //Ngambil mesin yang mau dijual
        $team_machine_useds = TeamMachine::where('is_used', 1)->get('id'); //Ambil semua mesin yang is_usednya 1

        foreach ($team_machine_useds as $team_machine_used) {
            if ($team_machine_used->id == $team_machine->id) {
                //Kalau ada berarti gk bisa dijual
                $status = 'error';
                $msg = "Penjualan mesin gagal dilakukan karena mesin dengan id " . $team_machine->id . " sedang digunakan";

                // Ambil team mesin
                $team_mesins = TeamMachine::where('team_id', $team->id)->where('season_sell', null)->get();
                $index = 0;

                foreach ($team_mesins as $team_mesin) {
                    //Ngambil mesin
                    $machine_name = Machine::where('id', $team_mesin->machine_id)->first()->name;
                    $team_mesins[$index]->name = $machine_name;
                    $index++;
                }

                return response()->json(array(
                    'status' => $status,
                    'msg' => $msg,
                    'team_mesins' => $team_mesins,
                ), 200);
            }
        }
        //Kalau tidak ada brarti lanjut proses
        $season_sell = SeasonNow::first()->number; //ambil season sekarang dan simpan di season_sell
        $season_buy = $team_machine->season_buy; //ambil season beli dari mesin
        $price_var = $team_machine->machine->price_var; //ambil harga jual
        $buy_price = $team_machine->machine->price; //ambil harga beli

        // Perhitungan Harga jual
        $dT = ($buy_price - $price_var) / 3;
        $sell_price = round($buy_price - ($season_sell * $dT), 2);
        //Tambah uang
        $team->tc = $team->tc + $sell_price;
        $team->total_income = $team->total_income + $sell_price;
        $team->save();

        //update season sell team machine (jual)
        $team_machine->season_sell = $season_sell;
        $team_machine->save();
        $status = "success";
        $msg = 'Penjualan mesin berhasil dilakukan';

        // Ambil team mesin
        $team_mesins = TeamMachine::where('team_id', $team->id)->where('season_sell', null)->get();

        // Masukkan detail machine kedalam array available machine
        $index = 0;
        foreach ($team_mesins as $team_mesin) {
            //Ngambil mesin
            $machine_name = Machine::where('id', $team_mesin->machine_id)->first()->name;
            $team_mesins[$index]->name = $machine_name;
            $index++;
        }
        // dd($team_mesins);

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
            'team_mesins' => $team_mesins,
        ), 200);
    }

    public function resetMachine(Request $request)
    {
        // Define Variable
        // IS_USED masih bermasalah cek lgi!
        $team = Auth::user()->team;
        $msg = "";
        $status = "";
        //Ambil mesin kombinasi tim saat ini

        //Ambil data dari request
        $jenis_kombinasi = $request['jenis_kombinasi']; //Isinya kitosan, udang, saus, ac, filter
        //101 --> saus tomat, 102 --> kitosan
        if ($jenis_kombinasi == "udang") {
            //Ambil semua mesin kecuali id 11,12 dan 15,16,17
            $all_team_mesins = $team->teamMachines->where('machine_id', '<', 15)->where('machine_id', '!=', 11)->where('machine_id', '!=', 12)->where('machine_id', '!=', 2)->where('machine_id', '!=', 4);
            // dd($all_team_mesins);
            foreach ($all_team_mesins as $team_mesin) {
                $team_mesin->is_used = 0;
                $team_mesin->save();
            }
            //Kalau sudah is_used jadi 0, delete kombinasinya
            DB::table('team_machine_combination')
                ->where('machine_combination_id', "!=", 101)
                ->where('machine_combination_id', "!=", 102)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($jenis_kombinasi == "saus") {
            //Ambil semua mesin dengan id 11 dan 12 
            $all_team_mesins = $team->teamMachines->where('machine_id', '>', 10)->where('machine_id', '<', 13);
            // dd($all_team_mesins);
            foreach ($all_team_mesins as $team_mesin) {
                $team_mesin->is_used = 0;
                $team_mesin->save();
            }
            //Kalau sudah is_used jadi 0, delete kombinasinya
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 101)
                ->where('team_id', $team->id)
                ->delete();
        } else if ($jenis_kombinasi == "kitosan") {
            //Ambil semua mesin dengan id 15 16 17
            $all_team_mesins = $team->teamMachines->where('machine_id', '>=', 15);
            // dd($all_team_mesins);
            foreach ($all_team_mesins as $team_mesin) {
                $team_mesin->is_used = 0;
                $team_mesin->save();
            }
            //Kalau sudah is_used jadi 0, delete kombinasinya
            DB::table('team_machine_combination')
                ->where('machine_combination_id', 102)
                ->where('team_id', $team->id)
                ->delete();
        }

        $msg = 'Berhasil melakukan reset kombinasi!';

        return response()->json(array(
            'status' => $status,
            'msg' => $msg,
        ), 200);
    }
}
