<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    public function ingridients()
    {
        return $this->belongsToMany(Ingridient::class, 'ingridient_season', 'season_id', 'ingridient_id')
            ->withPivot(['price']);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'season_id', 'product_id');
    }
}
