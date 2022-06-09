<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use App\MachineCombination;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function SusunMesin(Request $request){
        // Define banyak mesin berapa
        $banyak_machine = count($request->all());
        
        // Masukan order dari tiap mesin
        $orders = [];
        foreach ($request as $idx => $machine) {
            $orders[$idx+1] = $machine;
        }

        // Dapatkan semua kombinasi dari mesin yang berada pada order yang disusun
        $combinations = [];
        for ($i=1; $i <= $banyak_machine; $i++) {
            $combinations[] = $orders[$i]->machineCombinations()->withPivot('order', $i)->get(['id']);
        }

        // Lakukan intersect untuk mengetahui apakah ada kombinasi yang cocok
        $combination_found = array_intersect(...$combinations);
        
        // Apabila terdapat persis satu kombinasi yang cocok maka statusnya true
        $status = (count($combination_found)==1) ? true : false;

        // 
        if ($status){
            $combination = MachineCombination::find($combination_found[1]);
        }

        return response()->json([
            'status' => $status,
            'combination' => $combination,
        ]);
    }
}
