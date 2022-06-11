<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    public $timestamps = false;
    //
    public function teamMachines()
    {
        return $this->hasMany(TeamMachine::class, 'machine_id');
    }

    public function machineStore()
    {
        return $this->belongsToMany(MachineStore::class, 'machine_machine_store' ,'machine_id', 'machine_store_id')
        ->withPivot(['stock']);
    }

    public function machineCombinations()
    {
        return $this->belongsToMany(MachineCombinations::class, 'machine_machine_combination' ,'machine_id', 'machine_combination_id')
            ->withPivot(['order']);
    }
}
