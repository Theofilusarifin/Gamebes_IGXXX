<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'product_team','product_id', 'team_id')
            ->withPivot(['amount_have', 'amount_use', 'total']);
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'product_season', 'product_id', 'season_id');
    }
}
