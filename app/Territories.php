<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Territories extends Model
{
    //
    public function transportStore()
    {
        return $this->belongsTo(TransportStore::class, 'transport_store_id');
    }

    public function ingridientStore()
    {
        return $this->belongsTo(IngridientStore::class, 'ingridient_store_id');
    }

    public function machineStore()
    {
        return $this->belongsTo(MachineStore::class, 'machine_store_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'territory_id');
    }
}
