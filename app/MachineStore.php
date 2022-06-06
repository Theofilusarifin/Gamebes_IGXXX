<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineStore extends Model
{
    //
    public function territory()
    {
        return $this->belongsTo(Territories::class, 'machine_store_id');
    }

    public function machines()
    {
        return $this->hasMany(Machine::class, 'machine_store_id');
    }
}
