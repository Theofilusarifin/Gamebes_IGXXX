<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'team_id', 'product_id')
            ->withPivot(['amount_have', 'amount_sold']);
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'team_id', 'transport_id')
            ->withPivot(['amount', 'use_num']);
    }

    public function territory()
    {
        return $this->belongsTo(Territories::class, 'territory_id');
    }

    public function teamMachines()
    {
        return $this->hasMany(TeamMachine::class, 'team_id');
    }

    public function ingridients()
    {
        return $this->belongsToMany(Team::class, 'team_id', 'ingridient_id')
            ->withPivot(['amount']);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function investations()
    {
        return $this->belongsToMany(Team::class, 'team_id', 'investation_id') 
        ->withPivot(['total_profit']);
    }
}
