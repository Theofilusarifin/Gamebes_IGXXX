@extends('peserta.layouts.app')

@section('content')
<div class="row my-6">
    <div class="col-4 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Produk</h2>
                    </div>
                    <div class="col text-end">
                        <a href="#" class="btn btn-sm btn-primary">See all</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive py-4">
                <table class="table table-flush" id="datatable">
                    <thead class="thead-light">                        
                        <tr>
                            <th class="border-bottom" scope="col">Seafood Store</th>
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
                        <tr>
                            <th class="text-gray-900" scope="row">udang</th>
                            <td class="fw-bolder text-gray-500">60</td>
                            <td class="fw-bolder text-gray-500">120</td>
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

    <div class="col-4 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Ingredient</h2>
                    </div>
                    <div class="col text-end">
                        <a href="#" class="btn btn-sm btn-primary">See all</a>
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

@endsection