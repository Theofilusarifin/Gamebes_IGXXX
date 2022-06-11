<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngridientStore extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    public $keyType = 'string';
    public function ingridients()
    {
        return $this->belongsToMany(Ingridient::class, 'ingridient_store_id', 'ingridient_id')
        ->withPivot(['stock']);
    }

    public function territory()
    {
        return $this->hasOne(Territory::class, 'ingridient_store_id');
    }
}
