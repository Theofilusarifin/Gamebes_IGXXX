<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    public $timestamps = false;
    public function transportstores()
    {
        return $this->belongsToMany(TransportStore::class, 'transport_transport_store', 'transport_store_id', 'transport_id')
            ->withPivot(['stock']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'transport_team', 'transport_id', 'team_id')
            ->withPivot(['amount', 'use_num']);
    }
}
