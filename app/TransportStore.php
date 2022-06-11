<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportStore extends Model
{
    public $timestamps = false;
    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'transport_id', 'transport_store_id')
        ->withPivot(['stock']);
    }

    public function territories()
    {
        //How?
        return $this->hasOne(Territory::class, 'transport_store_id');
    }
}
