@extends('peserta.layouts.question')

@section('style')
<style>
    body {
        padding: 2rem 2rem 0;
    }

    .btn,
    h5 {
        color: #fff !important;
    }

    .card-header {
        background-color: #1F2937 !important;
        color: #fff !important;
    }

    .contest-container {
        display: flex;
        align-items: start;
    }

    .contest-content {
        width: 70%;
        order: 1;
    }

    .contest-nav {
        order: 2;
        width: 30%;
        box-shadow: 0 4px 24px 0 rgb(34 41 47 / 10%);
        background-color: white;
        padding: 2rem;
        margin-left: 2rem;
        margin-bottom: 2rem;
        border-radius: .428rem;
    }

    .mc-container {
        display: flex;
        align-items: start;
    }

    .mc-number {
        width: 20%;
        margin-right: 2rem;
    }

    .mc-content {
        width: 80%;
    }

    .mc-list-number {
        display: grid;
        grid-template-columns: auto auto auto auto auto;
    }

    .badge {
        margin: 2px;
    }

    .pilihan>.row>.col-auto {
        cursor: pointer;
    }


    @media (max-width: 1360px) {
        .mc-list-number {
            grid-template-columns: auto auto auto;
        }
    }

    @media (max-width: 1160px) {

        .contest-container {
            display: flex;
            flex-direction: column;
        }

        .contest-nav {
            width: 100%;
            margin-left: 0;
        }

        .contest-content {
            width: 100%;
        }

        .mc-list-number {
            grid-template-columns: auto auto;
        }
    }
</style>
@endsection

@section('content')

<div class='mc-container my-5 px-5'>

    {{-- Navigasi number --}}
    <div class="card mc-number">
        <div class="card-header d-flex justify-content-center align-items-center">
            <h5>Navigasi</h5>
        </div>
        <div class="card-body">
            <div class="card-text">
                <div class="inline-spacing">
                    <div class="mc-list-number">
                        {{-- Iterasi Question untuk tau banyaknya nomer --}}
                        @foreach($questions as $question)

                        {{-- Definisikan warna --}}
                        @php
                        // Default background putih text hitam
                        $classWarna = 'bg-light text-black';
                        if($question->number == $number) {
                        $classWarna = 'bg-primary text-white';
                        }
                        else if(isset($question->teams()->where('team_id',
                        Auth::user()->team->id)->first()->pivot->answer)){
                        $classWarna = 'bg-success text-white';
                        }
                        @endphp

                        <button form="submit_submission" style='border: none;' name="tujuan"
                            value='{{ $question->number }}' class="badge rounded-pill {{ " $classWarna" }}">
                            {{$question->number }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <br>
            @if (!is_null($previous))
            <button form="submit_submission" name="tujuan" value='{{ $previous }}' class="btn btn-secondary"
                style='float: left; padding: 5px 10px'>
                <span class='badge rounded-pill badge-light-secondary'>
                    <i data-feather='arrow-left'></i>
                </span>
            </button>
            @endif
            @if ($number != $last_number)
            <button form="submit_submission" name="tujuan" value='{{ $next }}' class="btn btn-secondary"
                style='float: right; padding: 5px 10px'>
                <span class='badge rounded-pill badge-light-secondary'>
                    <i data-feather='arrow-right'></i>
                </span>
            </button>
            @endif
        </div>
    </div>

    {{-- Soal dan Jawaban --}}
    <div class="mc-content">
        <div class="card">
            <div class="card-header px-4">
                <h5 class="px-4">Nomor {{ $number }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('peserta.investasi.submit') }}" method='POST' id='submit_submission'>
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $questionNow->id }}">
                    <div class="card-text px-4">
                        <br>
                        <p class="soal">
                            {!! $questionNow->description !!}
                        </p>
                        <hr>
                        <div class="pilihan">
                            @foreach ($questionNow->answers as $choice)
                            <br>
                            <div class="row">
                                <div class="col-auto">
                                    <input class="form-check-input mb-1" style="cursor: pointer !important" type="radio" id='pilihan_{{ $choice->id }}'
                                        name="answer" value="{{ Str::upper($choice->letter) }}"
                                        {{!is_null($currentSubmission) ? (Str::upper($currentSubmission->pivot->answer)
                                    == Str::upper($choice->letter) ? "checked" : "") : "" }}>
                                </div>
                                <div class='col-auto'>
                                    <label for='pilihan_{{ $choice->id }}' style="cursor: pointer !important">{{ $choice->letter}}.</label>
                                </div>
                                <div class="col-10">
                                    <label for='pilihan_{{ $choice->id }}' style="cursor: pointer !important">{{ $choice->answer }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </form>
                <div class="mt-3 mb-5 mx-4">
                    @if (!is_null($previous))
                    <button type='submit' form="submit_submission" name="tujuan" value='{{ $previous }}'
                        class="btn btn-secondary" style='float: left;'>Back</button>
                    @endif
                    @if ($number == $last_number)
                    <button type='button' data-bs-toggle="modal" data-bs-target="#finishAttemptModal"
                        class="btn btn-danger" style='float: right;'>Finish Attempt</button>
                    @else
                    <button type='submit' form="submit_submission" name="tujuan" value='{{ $next }}'
                        class="btn btn-success" style='float: right;'>Next</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
{{-- MODAL FINISH ATTEMPT --}}
<div class="modal fade" id="finishAttemptModal" tabindex="-1" aria-labelledby="finishAttemptModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Finish Attempt</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <div class="modal-body">
                <p>Submit All and Finish?</p>
            </div>
            <div class="modal-footer">
                <button class='btn btn-secondary' data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                <button type='submit' form="submit_submission" name="tujuan" value='end' class="btn btn-danger"
                    data-bs-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>
{{-- END OF MODAL FINISH ATTEMPT --}}
@endsection