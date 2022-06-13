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
                    <div class="row mb-2">
                        {{-- Pilih Item yang ingin dijual --}}
                        <div class="col-7">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="product">Pilih Produk</label>
                                <select class="form-select" id="product" aria-label="Default select example">
                                    <option selected value="">-- Pilih Nama Produk --</option>
                                    <option selected value="1">-- Udang Kaleng --</option>
                                    <option selected value="2">-- Kitosan --</option>
                                    <option selected value="3">-- Saos Tomat --</option>
                                </select>
                            </div>
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-5">
                            <label class="my-1 me-2" for="banyak_item">Banyak Produksi</label>
                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Produksi --"
                                id='banyak_item' required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="save_saus" style="width: 100px" type="button"
                                onclick="save('saus')" disabled>Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection