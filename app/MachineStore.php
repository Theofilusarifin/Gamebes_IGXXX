<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineStore extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';
    public function territory()
    {
        return $this->belongsTo(Territory::class, 'machine_store_id');
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_machine_store' , 'machine_store_id', 'machine_id')
        ->withPivot(['stock']);
    }
}
