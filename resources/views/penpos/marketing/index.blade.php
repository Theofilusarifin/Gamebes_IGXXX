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
@endsection

@section('content')
{{-- Card Kitosan --}}
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
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
                                <label class="my-1 me-2" for="team_id">Pilih Team</label>
                                <select class="form-select" id="team_id" aria-label="Default select example">
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
                        {{-- Pilih Item yang ingin dijual --}}
                        <div class="col-7">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="product_id">Pilih Produk</label>
                                <select class="form-select" id="product_id" aria-label="Default select example">
                                    <option selected disabled>-- Pilih Nama Produk --</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-5">
                            <label class="my-1 me-2" for="banyak_item">Banyak Penjualan</label>
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Penjualan --"
                                id='banyak_item' required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="jual" type="button" style="width: 100px" onclick="jual()">Jual</button>
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
    function jual() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.marketing.sell') }}",
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'team_id': $('#team_id').val(),
                'product_id': $('#product_id').val(),
                'banyak_item': $('#banyak_item').val(),
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