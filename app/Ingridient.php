<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingridient extends Model
{
    public function ingridientStore()
    {
        return $this->belongsTo(IngridientStore::class, 'ingridient_store_id');
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'ingridient_season', 'ingridient_id', 'season_id')
            ->withPivot(['price']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'ingridient_team', 'ingridient_id', 'team_id')
            ->withPivot(['amount']);
    }
}
