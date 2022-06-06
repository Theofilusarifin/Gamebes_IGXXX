<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    //
    public function transportstore()
    {
        return $this->belongsTo(TransportStore::class, 'transport_store_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'transport_id', 'team_id')
            ->withPivot(['amount', 'use_num']);
    }
}
