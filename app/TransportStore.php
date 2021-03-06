<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportStore extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';
    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'transport_transport_store',  'transport_store_id', 'transport_id')
            ->withPivot(['stock']);
    }

    public function territories()
    {
        //How?
        return $this->hasOne(Territory::class, 'transport_store_id');
    }
}
