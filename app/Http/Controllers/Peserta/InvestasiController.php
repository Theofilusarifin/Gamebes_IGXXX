<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Investation;
use App\Question;
use App\Season;
use App\SeasonNow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestasiController extends Controller
{
    public function authorization()
    {
        if (Auth::user()->role != "ketua") {
            return false;
        }
        return true;
    }

    public function game_authorization()
    {
        $season = Season::find(SeasonNow::first()->number);
        // Belum Mulai
        if ($season->number == 1 && $season->start_time == null && $season->end_time == null) {
            return false;
        }
        // Udah Selesai
        // Waktu di Surabaya sekarang
        $now = date('Y-m-d H:i:s');
        if ($season->end_time != null) {
            if ($season->number == 3 && $season->end_time < $now) {
                return false;
            }
        }
        return true;
    }

    public function index()
    {
        if(!$this->authorization()){
            return redirect()->back();
        }

        if (!$this->game_authorization()) {
            return redirect()->back();
        }

        $team = Auth::user()->team;

        $investation_team = $team->investations()->orderBy('id', 'ASC')->get();

        return view('peserta.investasi.index', compact('investation_team'));
    }

    public function show(Investation $investation, $number)
    {
        if (!$this->authorization()) {
            return redirect()->back();
        }

        $team = Auth::user()->team;
        $other_investation_on_doing = $team->investations->where('pivot.start', 1)->where('pivot.finish', 0)->where('id', "!=", $investation->id)->first();
        $investation_on_doing = $team->investations->where('id', $investation->id)->first();
        if ($investation_on_doing != null && $investation_on_doing->pivot->finish == 1) {
            return redirect()->route('peserta.investasi')->with("error", "Investasi ini sudah dikerjakan!");
        } else if ($other_investation_on_doing != null) {
            return redirect()->route('peserta.investasi')->with("error", "Selesaikan investasi $other_investation_on_doing->id terlebih dahulu!");
        } else {
            $investation->teams()->sync([
                $team->id =>
                [
                    'total_profit' => 0,
                    'start' => 1
                ],
            ], false);

            //To Do: urutkan sesuai number
            $questions = $investation->questions()->orderBy('number', 'ASC')->get();
            // dd($questions);
            $last_number = $questions->max('number');

            // dd($last_number);

            $previous = $questions->where('number', '<', $number)->max('number');

            // dd($previous);

            $next = $questions->where('number', '>', $number)->min('number');

            // dd($next);

            $questionNow = $questions->where('number', '=', $number)->first();
            // dd($questionNow);

            $currentSubmission = $questionNow->teams()->where('team_id', '=', Auth::user()->team->id)->first();

            return view('peserta.investasi.question', compact('investation', 'questions', 'last_number', 'previous', 'next', 'questionNow', 'number', 'currentSubmission'));
        }
    }

    public function submission(Request $request)
    {
        if (!$this->authorization()) {
            return redirect()->back();
        }

        $questionNow = Question::find($request['question_id']);

        $investation = Investation::find($questionNow->investation_id);

        $answer = $request['answer'];

        $team = Auth::user()->team; 
        if (isset($answer)) {

            //Get Jawaban Benar
            $user_answer = $questionNow->answers->where('letter', $answer)->first();

            $correct = $user_answer->letter == $questionNow->correct_answer ? true : false;

            //Get Skor
            if ($correct)
                $is_correct = 1;
            else
                $is_correct = 0;

            $questionNow->teams()->sync([
                $team->id =>
                [
                    'answer' => $answer,
                    'is_correct' => $is_correct
                ],
            ], false);
        }

        // Navigation
        $number = $request["tujuan"];

        $total_correct = $team->questions->where('investation_id', '=', $investation->id)->sum("pivot.is_correct");

        // dd($total_correct);
        // Logic Score
        $total_profit = $total_correct * ($investation->profit / 10);

        $team->investations()->sync([$investation->id => ['total_profit' => $total_profit]], false);

        // Jika bukan submit, di return langsung
        if ($number != 'end') return redirect(route('peserta.investasi.show', [$investation->id, $number]));

        // Jika end attempt, update total skor & waktu selesai
        $team->investations()->sync([$investation->id => ['finish' => 1]], false);
        $team->tc = $team->tc + $total_profit;
        $team->total_income = $team->total_income + $total_profit;
        $team->save();

        session()->flash("success", "Investasi berhasil diselesaikan");
        return redirect(route('peserta.investasi'));
    }
}
