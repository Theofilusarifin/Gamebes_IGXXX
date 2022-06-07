<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    public function territory()
    {
        return $this->hasOne(Territory::class, 'service_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'service_id');
    }
}
