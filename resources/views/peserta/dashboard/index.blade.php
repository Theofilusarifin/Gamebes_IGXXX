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
    <div class="row my-5 d-flex">
        <div class="col">
            <div class="card h-50">
                <svg class="icon icon-md col bg-success" width="100%" height="180">
                </svg>
                <div class="card-body">
                    <h4 class="card-title">Pemasukan {{ $teams->total_income }} TC</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-50">
                <svg class="icon icon-md col bg-danger" width="100%" height="180">
                </svg>
                <div class="card-body">
                    <h4 class="card-title">Pengeluaran {{ $teams->total_spend }} TC</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-50">
                <svg class="icon icon-md col bg-warning" width="100%" height="180">
                </svg>
                <div class="card-body">
                    <h4 class="card-title">Sisa Uang {{ $teams->tc }} TC</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-5 d-flex" style="margin-top:-100px">
        {{-- Card Pembelian --}}
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Pembelian</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Jumlah Dimiliki</th>
                                    <th class="border-bottom" scope="col">Total (TC)</th>
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
                                        @if ($toko_barangs_teams[$i-1][$j+$counter+2] == null)
                                        <td class="fw-bolder text-gray-500">0
                                        </td>
                                        @else
                                        </td>
                                        <td class="fw-bolder text-gray-500">{{$toko_barang_teams[$i-1][$j+$counter+2]}}
                                        </td>
                                        @endif
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
        {{-- Card Penjualan --}}
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Penjualan</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Jumlah Dimiliki</th>
                                    <th class="border-bottom" scope="col">Total (TC)</th>
                                </tr>
                            </thead>
                            @empty(!$data_team_juals)
                            <tbody>
                                @foreach ($data_team_juals as $product)
                                <tr>
                                    <th class="text-gray-900" scope="row">{{$product->name}}</th>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->amount_have}}</td>
                                    @if ($product->pivot->total == null)
                                    <td class="fw-bolder text-gray-500">0</td>
                                    @else
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->total}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            @endempty
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-5 d-flex">
        {{-- Card Perjalanan --}}
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Perjalanan</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Aktivitas</th>
                                    <th class="border-bottom" scope="col">Jumlah</th>
                                    <th class="border-bottom" scope="col">Total (TC)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-gray-900" scope="row">Menyebrang</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->total_spawn}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$teams->total_spawn*30}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Lurus</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->s_moves}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$teams->s_moves*1}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Serong</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->d_moves}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$teams->d_moves*3}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Denda</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->total_crash}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$teams->total_crash*8}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Total Susun Mesin</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->machine_assembly}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$teams->machine_assembly*5}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Jumlah Limbah</th>
                                    <td class="fw-bolder text-gray-500">{{$teams->waste}} Liter</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card Investasi --}}
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Investasi</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama Investasi</th>
                                    <th class="border-bottom" scope="col">Total (TC)</th>
                                </tr>
                            </thead>
                            @empty(!$profits)
                            <tbody>
                                @foreach ($profits as $key => $investasi )
                                <tr>
                                    <td class="text-gray-900" scope="row">Investasi {{$key}}</td>
                                    <td class="fw-bolder text-gray-500">{{$investasi}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endempty
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-5 d-flex">
        {{-- Card Penjualan Mesin --}}
        <div class="col-12 col-sm-12 col-xl-12">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Penjualan Mesin</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama Mesin</th>
                                    <th class="border-bottom" scope="col">Season Beli</th>
                                    <th class="border-bottom" scope="col">Season Jual</th>
                                    <th class="border-bottom" scope="col">Harga Jual (TC)</th>
                                </tr>
                            </thead>
                            @empty(!$hargaMesins)
                            <tbody>
                                @php
                                $counter = 0
                                @endphp
                                @for ($i = 0; $i < count($hargaMesins)/4; $i++) <tr>
                                    <th class="text-gray-900" scope="row">{{$hargaMesins[$counter]}}</th>
                                    <td class="fw-bolder text-gray-500">{{$hargaMesins[$counter+1]}}</td>
                                    <td class="fw-bolder text-gray-500">{{$hargaMesins[$counter+2]}}</td>
                                    <td class="fw-bolder text-gray-500">{{$hargaMesins[$counter+3]}}</td>
                                    </tr>
                                    @php
                                    $counter += 4
                                    @endphp
                                    @endfor
                            </tbody>
                            @endempty
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection