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
@endsection

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
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_ingridients as $ingridient)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{$ingridient->name}}</td>
                                    <td class="fw-bolder text-gray-500">{{$ingridient->pivot->amount_have}}</td>
                                </tr>
                                @endforeach
                            </tbody>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_products as $product)
                                <tr>
                                    <td class="fw-bolder text-gray-500">{{$product->name}}</td>
                                    <td class="fw-bolder text-gray-500">{{$product->pivot->amount_have}}</td>
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