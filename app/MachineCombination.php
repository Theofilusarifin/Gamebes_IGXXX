<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineCombination extends Model
{
    public $timestamps = false;
    //
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_machine_combination', 'machine_combination_id', 'machine_id')
            ->withPivot(['order']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_machine_combination', 'machine_combination_id', 'team_id');
    }
}
