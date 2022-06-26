<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Investation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestasiController extends Controller
{
    public function index(){
        $team = Auth::user()->team;

        $investation_team = $team->investations->all();
        return view('peserta.investasi.index', compact('investation_team'));
    }

    public function show(Investation $investation, $number){
        //To Do: urutkan sesuai number
        $questions = $investation->questions()->orderBy('number', 'ASC')->get();
        // dd($investation);
        // dd($questions);
        $last_number = $questions->max('number');

        // dd($last_number);

        $previous = $questions->where('number', '<', $number)->max('number');

        // dd($previous);

        $next = $questions->where('number', '>', $number)->min('number');

        // dd($next);

        $questionNow = $questions->where('number', '=', $number)->first();
        // dd($questionNow);

        // dd($questionNow->teams()->where('team_id', '=', 1)->first());

        $currentSubmission = $questionNow->teams()->where('team_id', '=', Auth::user()->team->id)->first();

        return view('peserta.investasi.question', compact('investation', 'questions', 'last_number', 'previous', 'next', 'questionNow', 'number', 'currentSubmission'));
    }
}
