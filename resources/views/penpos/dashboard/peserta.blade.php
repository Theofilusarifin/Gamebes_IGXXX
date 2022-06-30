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
@section('content')

<main class="px-5">

    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Dashboard Team</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            {{-- Pilih Team --}}
                            <div class="mb-4">
                                <label class="my-1 me-2" for="team_id">Pilih Team</label>
                                <select class="form-select" id="team_id" aria-label="Default select example" onchange="changeTeam()">
                                    <option selected disabled>-- Pilih Nama Team --</option>
                                    @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">
                                        {{ $team->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <form id="team_tujuan" action="{{ route('penpos.dashboard.peserta.data', [1]) }}">
                                <input class="btn btn-success" style="width: 100px" type="submit" value="Get Data"></input>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5 d-flex" style="">
        {{-- Card Pemasukan --}}
        <div class="col-12 col-sm-12 col-xl-4">
            <div class="card border-0 shadow">
                <div class="card-header" style="background-color: #10B981 !important;">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-12">
                            <h1 class="fs-5 fw-bold text-white mb-0">Pemasukan</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h4>{{ $team->total_income }} TC</h4>
                </div>
            </div>
        </div>

        {{-- Card Pengeluaran --}}
        <div class="col-12 col-sm-12 col-xl-4">
            <div class="card border-0 shadow">
                <div class="card-header" style="background-color: #E11D48 !important;">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-12">
                            <h1 class="fs-5 fw-bold text-white mb-0">Pengeluaran</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h4>{{ $team->total_spend }} TC</h4>
                </div>
            </div>
        </div>

        {{-- Card Sisa Uang --}}
        <div class="col-12 col-sm-12 col-xl-4">
            <div class="card border-0 shadow">
                <div class="card-header" style="background-color: #FBA918 !important;">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-12">
                            <h1 class="fs-5 fw-bold text-white mb-0">Sisa Uang</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h4>{{ $team->tc }} TC</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 d-flex" style="">
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
                                    <th class="border-bottom" scope="col">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_ingridients as $ingridient)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->pivot->amount_have }}</td>
                                </tr>
                                @endforeach
                                @foreach ($team_machines2 as $machine)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{ $machine->name }}</td>
                                    <td class="fw-bolder text-gray-500">1</td>
                                </tr>
                                @endforeach
                                @foreach ($team_transports as $transport)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{ $transport->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $transport->pivot->amount_have }}</td>
                                </tr>
                                @endforeach
                                @if($team_services != null)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{ $team_services->name }}</td>
                                    <td class="fw-bolder text-gray-500">1</td>
                                </tr>
                                @endif
                            </tbody>
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
                                    <th class="border-bottom" scope="col">Jumlah</th>
                                    <th class="border-bottom" scope="col">Total (TC)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_products as $product)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{$product->name}}</td>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->amount_sold}}</td>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->total}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-5 d-flex">
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
                            <tbody>
                                <tr>
                                    <td class="text-gray-900" scope="row">Modal Awal</td>
                                    <td class="fw-bolder text-gray-500">500</td>
                                </tr>
                                @foreach ($team_investations as $investasi)
                                <tr>
                                    <td class="text-gray-900" scope="row">{{$investasi->name}}</td>
                                    <td class="fw-bolder text-gray-500">{{$investasi->pivot->total_profit}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                                    <td class="fw-bolder text-gray-500">{{$team->total_spawn}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$team->total_spawn*30}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Lurus</th>
                                    <td class="fw-bolder text-gray-500">{{$team->s_moves}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$team->s_moves*1}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Serong</th>
                                    <td class="fw-bolder text-gray-500">{{$team->d_moves}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$team->d_moves*3}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Denda</th>
                                    <td class="fw-bolder text-gray-500">{{$team->total_crash}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$team->total_crash*8}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Total Susun Mesin</th>
                                    <td class="fw-bolder text-gray-500">{{$team->machine_assembly}} Kali</td>
                                    <td class="fw-bolder text-gray-500">{{$team->machine_assembly*5}}</td>
                                </tr>
                                <tr>
                                    <th class="text-gray-900" scope="row">Jumlah Limbah</th>
                                    <td class="fw-bolder text-gray-500">{{$team->waste}} Liter</td>
                                </tr>
                            </tbody>
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
                            <tbody>
                                @foreach ($team_machines as $machine)
                                <tr>
                                    <th class="text-gray-900" scope="row">{{$machine->name}}</th>
                                    <td class="fw-bolder text-gray-500">{{$arraySeason[$machine->season_buy]}}</td>
                                    <td class="fw-bolder text-gray-500">{{$arraySeason[$machine->season_sell]}}</td>
                                    <td class="fw-bolder text-gray-500">{{$machine->sell_price}}</td>
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

@section('script')
<script>
    function changeTeam() {
        let team_id = $('#team_id').val();
        $('#team_tujuan').attr('action', "/penpos/dashboard/peserta/"+team_id)
    }
</script>
@endsection