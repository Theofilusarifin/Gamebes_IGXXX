@extends('peserta.layouts.app')

@section('content')
<div class="row my-6">
    <div class="col-4 col-sm-6 col-xl-6 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="fs-5 fw-bold mb-0">Ingridient</h2>
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
                        <h2 class="fs-5 fw-bold mb-0">Product</h2>
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

@endsection