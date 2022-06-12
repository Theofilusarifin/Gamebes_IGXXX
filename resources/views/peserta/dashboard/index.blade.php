@extends('peserta.layouts.app')

@section('content')


<div class="row my-5">
    {{-- Pemasukan --}}
    <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
            <div class="card-body">
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
            <div class="card-body">
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
            <div class="card-body">
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
            <div class="table-responsive py-4">
                <table class="table table-flush" id="datatable">
                    @foreach ($toko_barang_teams as $toko)
                    <thead class="thead-light">                        
                        <tr>
                            <th class="border-bottom" scope="col">{{$toko}}</th>
                        </tr>
                    </thead>
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">Item</th>
                            <th class="border-bottom" scope="col">Quantity</th>
                            <th class="border-bottom" scope="col">Value (TC)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_team_belis as $ingridient)
                        <tr>
                            <th class="text-gray-900" scope="row">udang</th>
                            <td class="fw-bolder text-gray-500">60</td>
                            <td class="fw-bolder text-gray-500">120</td>
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
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
                            <th class="border-bottom" scope="col">Item</th>
                            <th class="border-bottom" scope="col">Quantity</th>
                            <th class="border-bottom" scope="col">Value (TC)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-gray-900" scope="row">Udang Kaleng</th>
                            <td class="fw-bolder text-gray-500">750</td>
                            <td class="fw-bolder text-gray-500">$20</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/forms.html</th>
                            <td class="fw-bolder text-gray-500">2,987</td>
                            <td class="fw-bolder text-gray-500">0</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/util.html</th>
                            <td class="fw-bolder text-gray-500">2,844</td>
                            <td class="fw-bolder text-gray-500">294</td>
                        </tr>
                    </tbody>
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
            <div class="table-responsive py-4">
                <table class="table table-flush" id="datatable">
                    <thead class="thead-light">                        
                        <tr>
                            <th class="border-bottom" scope="col">Activity</th>
                            <th class="border-bottom" scope="col">Quantity</th>
                            <th class="border-bottom" scope="col">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-gray-900" scope="row">Menyebrang</th>
                            <td class="fw-bolder text-gray-500">5x</td>
                            <td class="fw-bolder text-gray-500">2000 TC</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Lurus</th>
                            <td class="fw-bolder text-gray-500">2x</td>
                            <td class="fw-bolder text-gray-500">1500 TC</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">Serang</th>
                            <td class="fw-bolder text-gray-500">3x</td>
                            <td class="fw-bolder text-gray-500">10000 TC</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Biaya Penjualan</h2>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-bottom" scope="col">Item</th>
                            <th class="border-bottom" scope="col">Quantity</th>
                            <th class="border-bottom" scope="col">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/index.html</th>
                            <td class="fw-bolder text-gray-500">3,225</td>
                            <td class="fw-bolder text-gray-500">$20</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/forms.html</th>
                            <td class="fw-bolder text-gray-500">2,987</td>
                            <td class="fw-bolder text-gray-500">0</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/util.html</th>
                            <td class="fw-bolder text-gray-500">2,844</td>
                            <td class="fw-bolder text-gray-500">294</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/validation.html</th>
                            <td class="fw-bolder text-gray-500">2,050</td>
                            <td class="fw-bolder text-gray-500">$147</td>
                        </tr>
                        <tr>
                            <th class="text-gray-900" scope="row">/demo/admin/modals.html</th>
                            <td class="fw-bolder text-gray-500">1,483</td>
                            <td class="fw-bolder text-gray-500">$19</td>
                        </tr>
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
                    <h2 class="fs-5 fw-bold mb-0">Inventory</h2>
                </div>
            </div>
        </div>
        <div class="table-responsive py-4">
            <table class="table table-flush" id="datatable">
                <thead class="thead-light">                        
                    <tr>
                        <th class="border-bottom" scope="col">Page name</th>
                        <th class="border-bottom" scope="col">Page Views</th>
                        <th class="border-bottom" scope="col">Page Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/index.html</th>
                        <td class="fw-bolder text-gray-500">3,225</td>
                        <td class="fw-bolder text-gray-500">$20</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/forms.html</th>
                        <td class="fw-bolder text-gray-500">2,987</td>
                        <td class="fw-bolder text-gray-500">0</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/util.html</th>
                        <td class="fw-bolder text-gray-500">2,844</td>
                        <td class="fw-bolder text-gray-500">294</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/validation.html</th>
                        <td class="fw-bolder text-gray-500">2,050</td>
                        <td class="fw-bolder text-gray-500">$147</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/modals.html</th>
                        <td class="fw-bolder text-gray-500">1,483</td>
                        <td class="fw-bolder text-gray-500">$19</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-12 mb-4">
    <div class="card border-0 shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">Susun Mesin</h2>
                </div>
            </div>
        </div>
        <div class="table-responsive py-4">
            <table class="table table-flush" id="datatable">
                <thead class="thead-light">                        
                    <tr>
                        <th class="border-bottom" scope="col">Page name</th>
                        <th class="border-bottom" scope="col">Page Views</th>
                        <th class="border-bottom" scope="col">Page Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/index.html</th>
                        <td class="fw-bolder text-gray-500">3,225</td>
                        <td class="fw-bolder text-gray-500">$20</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/forms.html</th>
                        <td class="fw-bolder text-gray-500">2,987</td>
                        <td class="fw-bolder text-gray-500">0</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/util.html</th>
                        <td class="fw-bolder text-gray-500">2,844</td>
                        <td class="fw-bolder text-gray-500">294</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/validation.html</th>
                        <td class="fw-bolder text-gray-500">2,050</td>
                        <td class="fw-bolder text-gray-500">$147</td>
                    </tr>
                    <tr>
                        <th class="text-gray-900" scope="row">/demo/admin/modals.html</th>
                        <td class="fw-bolder text-gray-500">1,483</td>
                        <td class="fw-bolder text-gray-500">$19</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection