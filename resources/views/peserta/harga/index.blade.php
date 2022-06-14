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
    {{-- Card Season --}}
    <div class="col-12 col-sm-12 col-xl-12">
        <div class="card border-0 shadow">
            <div class="card-body d-flex align-items-center">
                <p class="me-2 d-flex align-items-center text-primary" style="margin-bottom:0"> 
                    Musim Sekarang :
                </p>
                @if ($season_now->number == 1)
                    <h5 style="margin-bottom:0">Musim Panas</h5>
                @elseif ($season_now->number == 2)
                    <h5 style="margin-bottom:0">Musim Hujan</h5>
                @elseif ($season_now->number == 3)
                    <h5 style="margin-bottom:0">Musim Salju</h5>
                @endif
            </div>
        </div>
    </div>
    <div class="row my-5 d-flex">
        {{-- Card Ingredient --}}
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
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga (TC)</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ingridients as $ingridient)
                                    <tr>
                                        <td class="fw-bolder text-gray-500">{{ $ingridient->name }}</td>
                                        <td class="fw-bolder text-gray-500">{{ $ingridient->pivot->price }}</td>
                                        <td class="fw-bolder text-gray-500">{{ $ingridient->pivot->price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card Mesin --}}
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Mesin</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga (TC)</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-5 d-flex">
        {{-- Card Transport --}}
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Transport</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga (TC)</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Card Service --}}
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Service</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga (TC)</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection