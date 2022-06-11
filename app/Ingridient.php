<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingridient extends Model
{
    public $timestamps = false;
    public function ingridientStores()
    {
        return $this->belongsToMany(IngridientStore::class, 'ingridient_ingridient_store', 'ingridient_id', 'ingridient_store_id')
            ->withPivot(['stock']);
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'ingridient_season', 'ingridient_id', 'season_id')
            ->withPivot(['price']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'ingridient_team', 'ingridient_id', 'team_id')
            ->withPivot(['amount_have', 'amount_use', 'total'])
            ->orderby('ingridient_store_id', 'asc');
    }
}
