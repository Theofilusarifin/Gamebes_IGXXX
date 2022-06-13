@extends('peserta.layouts.app')
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
@section('content')
<main class="px-5">
    {{-- Card Ingredient --}}
    <div class="row my-5 d-flex">
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Ingredient</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">NAMA</th>
                                    <th class="border-bottom" scope="col">JUMLAH DIMILIKI</th>
                                    <th class="border-bottom" scope="col">TOTAL (TC)</th>
                                </tr>
                            </thead>
                            @empty(!$data_team_belis)
                            <tbody>
                                @for($i = 1; $i <= 4; $i++) <tr>
                                    <th class="text-gray-900" scope="row">{{$table_store2[$i-1]}}</th>
                                    </tr>
                                    @php
                                    $counter = 0
                                    @endphp
                                    @for ($j = 0; $j < count($toko_barang_teams[$i-1])/3; $j++) <tr>
                                        <th class="text-gray-900" scope="row">{{$toko_barang_teams[$i-1][$j+$counter]}}
                                        </th>
                                        <td class="fw-bolder text-gray-500">{{$toko_barang_teams[$i-1][$j+$counter+1]}}
                                        </td>
                                        <td class="fw-bolder text-gray-500">{{$toko_barang_teams[$i-1][$j+$counter+2]}}
                                        </td>
                                        @php
                                        $counter += 2
                                        @endphp
                                        </tr>
                                        @endfor
                                        @endfor
                            </tbody>
                            @endempty
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card Product --}}
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Product</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">NAMA</th>
                                    <th class="border-bottom" scope="col">JUMLAH DIMILIKI</th>
                                    <th class="border-bottom" scope="col">TOTAL (TC)</th>
                                </tr>
                            </thead>
                            @empty(!$data_team_juals)
                            @foreach ($data_team_juals as $product)
                            <tbody>
                                <tr>
                                    <th class="text-gray-900" scope="row">{{$product->name}}</th>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->amount_have}}</td>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->total}}</td>
                                </tr>
                            </tbody>
                            @endforeach
                            @endempty
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection