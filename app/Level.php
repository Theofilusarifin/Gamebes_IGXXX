<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    public $timestamps = false;
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_level', 'level_id', 'team_id')
            ->withPivot(['syarat_1', 'syarat_2', 'syarat_3']);
    }
}
