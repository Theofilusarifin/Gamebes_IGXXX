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
@endsection

@section('content')
{{-- Card Kitosan --}}
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Marketing</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Alert --}}
                    @include('penpos.layouts.alerts')

                    <div class="row">
                        <div class="col-12">
                            {{-- Pilih Team --}}
                            <div class="mb-4">
                                <label class="my-1 me-2" for="transport_id">Pilih Transport</label>
                                <select class="form-select" id="transport_id" aria-label="Default select example" onchange="getCooldown()">
                                    <option selected disabled>-- Pilih Transport --</option>
                                    @foreach ($team_transports as $transport)
                                    <option value="{{ $transport->pivot->id }}">
                                        {{ $transport->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between">
                            <label style="text-left">
                                <span id="marketing_cooldown"></span>
                            </label>
                            <script>
                                function CountDownTimer(id, team_transport_id, cooldown_marketing)
                                {
                                    if(cooldown_marketing != ''){
                                        var end = new Date(cooldown_marketing);
                                        var _second = 1000;
                                        var _minute = _second * 60;
                                        var _hour = _minute * 60;
                                        var timer;
                                        function showRemaining() {
                                            var now = new Date();
                                            var distance = end - now;
                                            if (distance < 0) {
                                                nullCooldown(team_transport_id);
                                                document.getElementById(id).innerHTML = "Bisa melakukan marketing!";
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

                                            document.getElementById(id).innerHTML = 'Countdown = ';
                                            document.getElementById(id).innerHTML += minutes + ':';
                                            document.getElementById(id).innerHTML += seconds;
                                        }
                                        timer = setInterval(showRemaining, 1000);
                                    }
                                    else{
                                        document.getElementById(id).innerHTML = "Bisa melakukan marketing!";
                                    }
                                }
                            </script>
                        </div>
                    </div>

                    {{-- Udang kaleng --}}
                    <div class="row d-flex align-items-center justify-content-center mt-2">
                        <div class="col-4" style="text-align:center">
                            <p class="mb-2 ms-2" style="font-weight: 800">Udang Kaleng</p>
                            <img src="{{ asset('assets/img/icons/products/Udang Kaleng.png') }}" alt="">
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-8 d-flex align-items-center mt-3">
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Penjualan --"
                                id='banyak_item_1'>
                        </div>
                    </div>

                    {{-- Saos Tomat --}}
                    <div class="row d-flex align-items-center justify-content-center mt-4">
                        <div class="col-4" style="text-align:center">
                            <p class="mb-2 ms-2" style="font-weight: 800">Saos Udang</p>
                            <img src="{{ asset('assets/img/icons/products/Saos Tomat.png') }}" alt="">
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-8 d-flex align-items-center mt-3">
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Penjualan --"
                                id='banyak_item_3'>
                        </div>
                    </div>

                    {{-- Kitosan --}}
                    <div class="row d-flex align-items-center justify-content-center mt-4">
                        <div class="col-4" style="text-align:center">
                            <p class="mb-2 ms-2" style="font-weight: 800">Kitosan</p>
                            <img src="{{ asset('assets/img/icons/products/Kitosan.png') }}" alt="">
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-8 d-flex align-items-center mt-3">
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Penjualan --"
                                id='banyak_item_2'>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="jual" type="button" style="width: 100px"
                                onclick="jual()">Jual</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
    function getCooldown() {
        $.ajax({
            type: 'POST',
            url: "{{ route('peserta.marketing.cooldown') }}",
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'transport_id': $('#transport_id').val(),
            },
            success: function (data) {
                CountDownTimer('marketing_cooldown', data.team_transport.id, data.team_transport.cooldown_maintenance);
            }
        });
    }

    function nullCooldown(team_transport_id) {
        $.ajax({
            type: 'POST',
            url: "{{ route('peserta.marketing.null.cooldown') }}",
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'team_transport_id': team_transport_id,
            },
            success: function (data) {
            }
        });
    }
    
    function jual() {
        $('#jual').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: "{{ route('peserta.marketing.sell') }}",
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'product_id': $('#product_id').val(),
                'transport_id': $('#transport_id').val(),
                'banyak_item_1': $('#banyak_item_1').val(),
                'banyak_item_2': $('#banyak_item_2').val(),
                'banyak_item_3': $('#banyak_item_3').val(),
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
                $('#jual').attr('disabled',false);
            }
        });
    }
</script>
@endsection