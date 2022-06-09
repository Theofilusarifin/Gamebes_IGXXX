<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;
    public function answers(){
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function questions()
    {
        return $this->belongsTo(Question::class, 'investation_id');
    }
}
