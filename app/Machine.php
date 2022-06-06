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
}
