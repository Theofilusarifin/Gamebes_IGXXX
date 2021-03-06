<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamMachine extends Model
{
    public $timestamps = false;
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id')
            ->orderby('machine_id', 'asc');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
}
