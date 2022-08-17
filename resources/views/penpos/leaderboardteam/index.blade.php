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
                        <div class="row">
                            <div class="col-12">
                                {{-- Pilih Team --}}
                                <div class="mb-4">
                                    <form action="" method="post">
                                        <label class="my-1 me-2" for="waste_variable">Waste Variable</label>
                                        <div class="row d-flex justify-content-center align-items-center">
                                            <div class="col-8">
                                                <input class="form-control" type="number" value="{{ $leaderboard->waste_variable }}" name="waste_variable"
                                                    id="waste_variable">
                                            </div>
                                            <div class="col-4">
                                                <input class="btn btn-success" type="submit" name="submit"
                                                    value="Submit">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Rank
                                    </th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Nama Tim
                                    </th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Tingkat
                                        Efetivitas</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Tingkat
                                        Higenis</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Saldo
                                        Akhir</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Poin
                                        Level</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Total
                                        Produk</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Total
                                        Mesin</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Jenis
                                        Transport</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Poin
                                        Tambahan</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Poin
                                        Limbah</th>
                                    <th class="border-bottom" scope="col" style="width: 5%; text-align:center">Total
                                        Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teams as $team)
                                <tr>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $loop->index+1 }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->name }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->effectivity }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->higenity }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->saldo_akhir }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->score }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->product }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->mesin }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->transport }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->score_tambahan }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->waste }}</td>
                                    <td class="fw-bolder text-gray-500" style="width:5%; text-align:center">{{
                                        $team->score_total }}</td>
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