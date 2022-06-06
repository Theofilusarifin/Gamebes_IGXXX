<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineCombination extends Model
{
    //
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_combination_id', 'machine_id')
            ->withPivot(['order']);
    }
}
