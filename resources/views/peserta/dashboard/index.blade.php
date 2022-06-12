@extends('peserta.layouts.app')

@section('content')


<div class="row my-5">
    {{-- Pemasukan --}}
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body col bg-success">
                <div class="row d-block d-xl-flex align-items-center">
                    <div
                        class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0"><svg class="icon icon-md"
                                fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h5">Total Pemasukkan</h2>
                            <h3 class="fw-extrabold mb-1"> {{ $teams->total_income }} TC</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Pengeluaran --}}
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body col bg-danger">
                <div class="row d-block d-xl-flex align-items-center">
                    <div
                        class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0"><svg class="icon icon-md"
                                fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                </path>
                            </svg></div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h5">Total Pengeluaran</h2>
                            <h3 class="fw-extrabold mb-1">{{ $teams->total_spend }} TC</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Sisa Uang --}}
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body col bg-warning">
                <div class="row d-block d-xl-flex align-items-center">
                    <div
                        class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                        <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0"><svg class="icon icon-md"
                                fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                </path>
                            </svg></div>
                    </div>
                    <div class="col-12 col-xl-7 px-xl-0">
                        <div class="d-none d-sm-block">
                            <h2 class="h5">Sisa Uang</h2>
                            <h3 class="fw-extrabold mb-1">{{ $teams->tc }} TC</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row my-6">
    <div class="col-4 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Pembelian</h2>
                    </div>
                </div>
            </div>
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
                        @for($i = 1; $i <= 4; $i++)
                        <tr>
                            <th class="text-gray-900" scope="row">{{$table_store2[$i-1]}}</th>
                        </tr>
                            @php
                                $counter = 0    
                            @endphp
                            @for ($j = 0; $j < count($toko_barang_teams[$i-1])/3; $j++)
                                <tr>
                                    <th class="text-gray-900" scope="row">{{$toko_barang_teams[$i-1][$j+$counter]}}</th>
                                    <td class="fw-bolder text-gray-500">{{$toko_barang_teams[$i-1][$j+$counter+1]}}</td>
                                    <td class="fw-bolder text-gray-500">{{$toko_barang_teams[$i-1][$j+$counter+2]}}</td>
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
    <div class="col-4 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Penjualan</h2>
                    </div>
                </div>
            </div>
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

<div class="row my-6">
    <div class="col-12 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Perjalanan</h2>
                    </div>
                </div>
            </div>
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
                            <td class="fw-bolder text-gray-500">{{$teams->total_spawn}}</td>
                            <td class="fw-bolder text-gray-500">{{$teams->total_spawn*30}}</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Lurus</th>
                            <td class="fw-bolder text-gray-500">{{$teams->s_moves}}</td>
                            <td class="fw-bolder text-gray-500">{{$teams->s_moves*1}}</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Serong</th>
                            <td class="fw-bolder text-gray-500">{{$teams->d_moves}}</td>
                            <td class="fw-bolder text-gray-500">{{$teams->d_moves*3}}</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Denda</th>
                            <td class="fw-bolder text-gray-500">{{$teams->total_crash}}</td>
                            <td class="fw-bolder text-gray-500">{{$teams->total_crash*8}}</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Total Susun Mesin</th>
                            <td class="fw-bolder text-gray-500">{{$teams->machine_assembly}}</td>
                            <td class="fw-bolder text-gray-500">{{$teams->machine_assembly*5}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Investasi</h2>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">Nama Investasi</th>
                            <th class="border-bottom" scope="col">Total (TC)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @empty(!$profits)
                            @for ($i = 1; $i <= 5; $i++)
                                @empty(!$profits[$i])
                                    <tr>
                                        <th class="text-gray-900" scope="row">Investasi {{$i}}</th>
                                        <td class="fw-bolder text-gray-500">{{$profits[$i]}}</td>
                                    </tr>
                                @endempty
                            @endfor
                        @endempty
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-xl-4 mb-4">
    <div class="card border-0 shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Penjualan Mesin</h2>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="border-bottom" scope="col">Nama Investasi</th>
                        <th class="border-bottom" scope="col">Total (TC)</th>
                    </tr>
                </thead>
                <tbody>
                    @empty(!$profits)
                        @for ($i = 1; $i <= 5; $i++)
                            @empty(!$profits[$i])
                                <tr>
                                    <th class="text-gray-900" scope="row">Investasi {{$i}}</th>
                                    <td class="fw-bolder text-gray-500">{{$profits[$i]}}</td>
                                </tr>
                            @endempty
                        @endfor
                    @endempty
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection