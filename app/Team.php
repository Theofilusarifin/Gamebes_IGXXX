<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Team extends Model
{
    public $timestamps = false;
    public function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_team', 'team_id', 'product_id')
            ->withPivot(['amount_have', 'amount_sold', 'total']);
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'transport_team', 'team_id', 'transport_id')
            ->withPivot(['amount_have', 'use_num']);
    }

    public function territory()
    {
        return $this->belongsTo(Territory::class, 'territory_id');
    }

    public function teamMachines()
    {
        return $this->hasMany(TeamMachine::class, 'team_id')
            ->orderby('machine_id', 'asc');
    }

    public function ingridients()
    {
        return $this->belongsToMany(Ingridient::class, 'ingridient_team', 'team_id', 'ingridient_id')
            ->withPivot(['amount_have', 'amount_use', 'total', 'expired_time'])
            ->orderby('ingridient_id', 'asc');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function investations()
    {
        return $this->belongsToMany(Investation::class, 'investation_team', 'team_id', 'investation_id')
            ->withPivot(['total_profit', 'start', 'finish'])
            ->orderby('investation_id', 'asc');
    }

    public function machineCombinations()
    {
        return $this->belongsToMany(MachineCombination::class, 'team_machine_combination', 'team_id', 'machine_combination_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'team_question', 'team_id', 'question_id')
            ->withPivot(['answer', 'is_correct']);
    }

    public function levels()
    {
        return $this->belongsToMany(Level::class, 'team_level', 'team_id', 'level_id')
            ->withPivot(['syarat_1', 'syarat_2', 'syarat_3', 'syarat_4']);
    }
}
