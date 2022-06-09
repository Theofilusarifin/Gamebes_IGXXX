<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportStore extends Model
{
    public $timestamps = false;
    public function transports()
    {
        return $this->hasMany(Transport::class, 'transport_store_id');
    }

    public function territories()
    {
        //How?
        return $this->hasOne(Territory::class, 'transport_store_id');
    }
}
