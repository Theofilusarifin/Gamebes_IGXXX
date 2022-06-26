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
@endsection

@section('content')
<main class="px-5">
    <div class="row my-5 d-flex">
        {{-- Card Investasi --}}
        <div class="col-12 col-sm-12 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex justify-content-start align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Investasi</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-bottom" scope="col">Jenis</th>
                                    <th class="border-bottom" scope="col">Profit</th>
                                    <th class="border-bottom" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                @if (count($investation_team) >= $i+1)
                                    <tr>
                                        <td class="fw-bolder">Investasi {{ $i+1 }}</td>
                                    @if ($investation_team[$i]->start != null)
                                    
                                    @else
                                        <td class="fw-bolder text-success">{{ $investation_team[$i]->profit }} TC</td>
                                        <td class="fw-bolder text-gray-500">
                                            <button disabled class="btn btn-secondary" type="button">Start</button>
                                        </td>
                                    </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td class="fw-bolder">Investasi {{ $i+1 }}</td>
                                        <td class="fw-bolder text-gray-500">Investasi belum dikerjakan</td>
                                        <td class="fw-bolder">
                                            <form action="{{ route('peserta.investasi.show', [$i, 1]) }}">
                                                <input type="submit" class="btn btn-secondary" value="Start">
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection