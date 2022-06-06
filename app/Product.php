<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'product_id', 'team_id')
            ->withPivot(['amount_have', 'amount_sold']);
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'product_id', 'season_id');
    }
}
