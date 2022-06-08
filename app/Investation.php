<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investation extends Model
{
    public function questions(){
        return $this->hasMany(Question::class, 'investation_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'investation_team', 'investation_id', 'team_id')
        ->withPivot(['total_profit']);
    }
}
