@extends('penpos.layouts.app')

@section('style')
<style>
    .btn {
        color: #fff !important;
    }

    .card-header {
        background-color: #1F2937 !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<main class="row px-5">
    <div class="row my-5">
        {{-- Card Leaderboard --}}
        <div class="col-12 col-sm-12 col-xl-12">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Leaderboard</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Rank</th>
                                    <th class="border-bottom" scope="col">Nama Tim</th>
                                    <th class="border-bottom" scope="col">Total Score</th>
                                    <th class="border-bottom" scope="col">Rank</th>
                                    <th class="border-bottom" scope="col">Nama Tim</th>
                                    <th class="border-bottom" scope="col">Total Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i <= 19; $i++) <tr>
                                    <td class="fw-bolder text-gray-500">{{ $i+1 }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $teams[$i]->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $teams[$i]->score }}</td>
                                    @if(!($i + 20 > count($teams)-1))
                                    <td class="fw-bolder text-gray-500">{{ $i+21 }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $teams[$i+20]->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $teams[$i+20]->score }}</td>
                                    @endif
                                    </tr>
                                    @endfor
                                    {{-- @foreach ($teams as $team)
                                    @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection