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
    {{-- Card Ingredient --}}
    <div class="pe-4 mt-5 mb-4">
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="row d-flex justify-content-start align-items-center">
                    {{-- Judul --}}
                    <div class="col-6">
                        <h1 class="fs-5 fw-bold text-white mb-0">Machine Description</h1>
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
                                <th class="border-bottom" scope="col">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($machines as $machine)
                            <tr>
                                <td class="fw-bolder text-gray-500">
                                    <img src="{{ asset('/assets/img/icons/machines/'.$machine->name.".png")
                                        }}" alt="">
                                </td>
                                <td class="fw-bolder text-gray-500">{{ $machine->name }}</td>
                                <td class="fw-bolder text-gray-500">{{ $machine->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection