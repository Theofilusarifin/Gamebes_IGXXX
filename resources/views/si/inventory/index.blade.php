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

    img:not(.logo) {
        max-width: 50px;
        width: 50px;
        height: auto;
    }
</style>
@endsection

@section('content')
<main class="px-5">
    {{-- Select Team --}}
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Inventory Team</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            {{-- Pilih Team --}}
                            <div class="">
                                <form action="{{ route('penpos.inventory', [1]) }}" id="form_inventory" method="get">
                                    <label class="my-1 me-2" for="team_id">Pilih Team</label>
                                    <select class="form-select" id="team_id" aria-label="Default select example"
                                        onchange="changeRoute()">
                                        <option selected disabled>-- Pilih Nama Team --</option>
                                        @foreach ($teams as $team)
                                        <option value="{{ $team->id }}">
                                            {{ $team->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="col-12 d-flex justify-content-end">
                                        <input type="submit" class="btn btn-success mt-3" style="float:right"
                                            value="Get Data">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    @include('peserta.layouts.alerts')
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">Nama</th>
                                    <th class="border-bottom" scope="col">Jumlah</th>
                                    <th class="border-bottom" scope="col">Waktu Expired</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_ingridients as $ingridient)
                                <tr id="row_{{$loop->index}}">
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/ingridients/'.$ingridient->name."
                                            .png")}}" alt="">
                                    </td>
                                    <td class="fw-bolder text-gray-500">{{$ingridient->name}}</td>
                                    <td class="fw-bolder text-gray-500">{{$ingridient->pivot->amount_have}}</td>
                                    <td>
                                        <script>
                                            CountDownTimer('countdown_{{$loop->index}}');
                                            function CountDownTimer(id)
                                            {
                                                var end = new Date('{{$ingridient->pivot->expired_time}}');
                                                var _second = 1000;
                                                var _minute = _second * 60;
                                                var _hour = _minute * 60;
                                                var timer;
                                                function showRemaining() {
                                                    var now = new Date();
                                                    var distance = end - now;
                                                    if (distance < 0) {
                                                        document.getElementById(id).innerHTML = "00:00";
                                                        deleteTeamIngridient('row_{{$loop->index}}', '{{$ingridient->pivot->team_id}}','{{$ingridient->id}}', '{{$ingridient->pivot->expired_time}}')
                                                        return;
                                                    }
                                                    var minutes = Math.floor((distance % _hour) / _minute);
                                                    var seconds = Math.floor((distance % _minute) / _second);

                                                    if (seconds < 10){
                                                        seconds = "0"+seconds;
                                                    }

                                                    if (minutes < 10){
                                                        minutes = "0"+minutes;
                                                    }

                                                    document.getElementById(id).innerHTML = minutes + ':';
                                                    document.getElementById(id).innerHTML += seconds;
                                                }
                                                timer = setInterval(showRemaining, 1000);
                                            }
                                        </script>
                                        <div class="fw-bolder" id="countdown_{{$loop->index}}">
                                    </td>
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
                                    <th class="border-bottom" scope="col">Gambar</th>
                                    <th class="border-bottom" scope="col">NAMA</th>
                                    <th class="border-bottom" scope="col">JUMLAH DIMILIKI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team_products as $product)
                                <tr>
                                    <td class="fw-bolder text-gray-500">
                                        <img src="{{ asset('/assets/img/icons/products/'.$product->name." .png") }}"
                                            alt="">
                                    </td>
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

@section('script')
<script>
    function changeRoute() {
        $('#form_inventory').attr('action', "/penpos/inventory/" + $('#team_id').val());
    }

    function deleteTeamIngridient(row_id, team_id, ingridient_id, expired_time) {
            // Hapus Row
            $('#'+row_id).remove();
            
            // AJAX untuk Hapus Data di Database
            $.ajax({
                type: 'POST',
                url: "{{ route('penpos.inventory.expired') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': team_id,
                    'ingridient_id': ingridient_id,
                    'expired_time': expired_time,
                },
                success: function (data) {
                    if (data.status != ""){

                        $('#alert').hide();
                        $('#alert').show();
                        $('#alert-body').html(data.msg);
                    
                        $("#alert").fadeTo(5000, 500).hide(1000, function(){
                            $("#alert").hide(1000);
                        });
                        if (data.status == "success") {
                            $('#alert').removeClass("alert-danger");
                            $('#alert').addClass("alert-success");
                        }
                        else if (data.status == "error") {
                            $('#alert').removeClass("alert-success");
                            $('#alert').addClass("alert-danger");
                        }
                    }
                }
            });
        }
</script>
@endsection