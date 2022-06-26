<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;
    public function answers(){
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function investation()
    {
        return $this->belongsTo(Question::class, 'investation_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_question', 'question_id', 'team_id')
        ->withPivot(['answer', 'is_correct']);
    }
}
