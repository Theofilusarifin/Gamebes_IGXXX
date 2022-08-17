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

    img:not(.logo) {
        max-width: 50px;
        width: 50px;
        height: auto;
    }
</style>
@section('content')
<main class="px-5">
    <div class="">
        {{-- Card Ingredient --}}
        <div class="pe-4 mt-5 mb-4" style="float:left; width:50%">
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
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga</th>
                                    <th class="border-bottom" scope="col">Satuan</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ingridients as $ingridient)
                                <tr>
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/ingridients/'.$ingridient->name.".png")
                                            }}" alt="">
                                    </td>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->pivot->price }} TC</td>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->packet }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $ingridient->stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Mesin --}}
        <div class="ps-4 mt-5 mb-4" style="float:right; width:50%">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Machine</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($machines as $machine)
                                <tr>
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/machines/'.$machine->name.'.png') }}" alt="">
                                    </td>
                                    <td class="fw-bolder text-gray-500">{{ $machine->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $machine->price }} TC</td>
                                    <td class="fw-bolder text-gray-500">{{ $machine->stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Product --}}
        <div class="pe-4 my-4" style="float:left; width:50%">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Harga Jual</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/products/'.$product->name.".png") }}"
                                            alt="">
                                    </td>
                                    <td class="fw-bolder text-gray-500">{{ $product->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $product->pivot->price }} TC</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Transport --}}
        <div class="pe-4 my-4" style="float:left; width:50%">
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
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transports as $transport)
                                <tr>
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/transports/'.$transport->name.'.png') }}" alt="">
                                    </td>
                                    <td class="fw-bolder text-gray-500">{{ $transport->name }}</td>
                                    <td class="fw-bolder text-gray-500">{{ $transport->price }} TC</td>
                                    <td class="fw-bolder text-gray-500">{{ $transport->stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Service --}}
        <div class="ps-4 my-4" style="float:right; width:50%">
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
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Harga</th>
                                    <th class="border-bottom" scope="col">Stock Tersedia</th>
                                </tr>
                            </thead>
                            @if ($services != null)
                            <tr>
                                <td class="fw-bolder text-gray-500">
                                    <img src="{{ asset('/assets/img/icons/services/Jasa Pembersih.png') }}" alt="">
                                </td>
                                <td class="fw-bolder text-gray-500">Jasa Pembersih</td>
                                <td class="fw-bolder text-gray-500">{{ $services->price }} TC</td>
                                <td class="fw-bolder text-gray-500">{{ $services->total_stock }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection