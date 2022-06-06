<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    //
    public function teamMachines()
    {
        return $this->hasMany(TeamMachine::class, 'machine_id');
    }

    public function machineStore()
    {
        return $this->belongsTo(machineStore::class, 'machine_store_id');
    }
}
