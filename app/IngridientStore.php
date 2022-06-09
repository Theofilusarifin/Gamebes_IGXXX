<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngridientStore extends Model
{
    public $timestamps = false;
    public function ingridients()
    {
        return $this->hasMany(Ingridient::class, 'ingridient_store_id');
    }

    public function territory()
    {
        return $this->hasOne(Territory::class, 'ingridient_store_id');
    }
}
