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
                                    <th class="border-bottom" scope="col">Tingkat Efetivitas</th>
                                    <th class="border-bottom" scope="col">Tingkat Higenis</th>
                                    <th class="border-bottom" scope="col">Saldo Akhir</th>
                                    <th class="border-bottom" scope="col">Limbah</th>
                                    <th class="border-bottom" scope="col">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teams as $team)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{ $loop->index+1 }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->effectivity }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->higenity }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->tc }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->waste }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $team->score }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection