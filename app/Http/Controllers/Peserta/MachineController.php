<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function SusunMesin(Request $request){
        $banyak_machine = count($request->all());
        $machines = [];
        foreach ($request as $idx => $machine) {
            $machines[$idx] = $machine;
        }

        for ($i=0; $i < $banyak_machine; $i++) { 
            # code...
        }
        $combination_1 = $machine_1->machineCombinations->withPivot('order', $machine_1->order);
        $combination_2 = $machine_2->machineCombinations->withPivot('order', $machine_2->order);
        $combination_3 = $machine_3->machineCombinations->withPivot('order', $machine_3->order);
        $combination_4 = $machine_4->machineCombinations->withPivot('order', $machine_4->order);
        $combination_5 = $machine_5->machineCombinations->withPivot('order', $machine_5->order);
        $combination_6 = $machine_6->machineCombinations->withPivot('order', $machine_6->order);
        $combination_7 = $machine_7->machineCombinations->withPivot('order', $machine_7->order);
        $combination_8 = $machine_8->machineCombinations->withPivot('order', $machine_8->order);
        $combination_9 = $machine_9->machineCombinations->withPivot('order', $machine_9->order);
        $combination_10 = $machine_10->machineCombinations->withPivot('order', $machine_10->order);   

    }
}
