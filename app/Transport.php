<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    public $timestamps = false;
    public function transportStores()
    {
        return $this->belongsToMany(TransportStore::class, 'transport_transport_store', 'transport_id', 'transport_store_id')
            ->withPivot(['stock']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'transport_team', 'transport_id', 'team_id')
            ->withPivot(['amount_have', 'use_num']);
    }
}
