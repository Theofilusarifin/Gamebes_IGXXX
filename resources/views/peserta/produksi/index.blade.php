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


    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    td {
        width: 10px;
        height: 10px;
    }
</style>
@section('content')
{{-- Card Produksi --}}
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Produksi</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Alert --}}
                    @include('peserta.layouts.alerts')
                    <div class="row mb-2">
                        {{-- Pilih Item yang ingin dijual --}}
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="product_id">Pilih Produk</label>
                                <select class="form-select" id="product_id" aria-label="Default select example">
                                    <option selected value="">-- Pilih Nama Produk --</option>
                                    <option value="1">Udang Kaleng</option>
                                    <option value="2">Kitosan</option>
                                    <option value="3">Saos Tomat</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-2">
                        {{-- Pilih Item yang ingin dijual --}}
                        <div class="col-7">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="udang_id">Pilih Udang</label>
                                <select class="form-select" id="udang_id" aria-label="Default select example">
                                    <option selected value="">-- Pilih Nama Udang --</option>
                                    <option value="1">Udang Vaname</option>
                                    <option value="2">Udang Pama</option>
                                    <option value="3">Udang Jerbung</option>
                                </select>
                            </div>
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-5">
                            <label class="my-1 me-2" for="banyak_produksi">Banyak Produksi</label>
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Produksi --"
                                id='banyak_produksi' required="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="produksi" style="width: 100px" type="button"
                                onclick="produksi()">Produksi</button>
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
    function produksi() {
            $.ajax({
                type: 'POST',
                url: "{{ route('peserta.produksi.produk') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'product_id': $('#product_id').val(),
                    'banyak_produksi': $('#banyak_produksi').val(),
                    'tipe_udang' : $('#udang_id').val(),
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