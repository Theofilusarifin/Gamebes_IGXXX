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
{{-- Card Investasi --}}
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Investasi</h1>
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
                        {{-- Pilih Investasi --}}
                        <div class="col-7">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="investasi_id">Pilih Investasi</label>
                                <select class="form-select" id="investasi_id" aria-label="Default select example">
                                    <option selected disabled>-- Pilih Investasi --</option>
                                    @foreach ($investations as $investation)
                                    <option value="{{ $investation->id }}">
                                        {{ $investation->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-5">
                            <label class="my-1 me-2" for="nilai_investasi">Nilai Investasi</label>
                            <input class="form-control" type="number" min=0 placeholder="-- Nilai Investasi --" id='nilai_investasi' required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="save_saus" style="width: 100px" type="button" onclick="save()">Save</button>
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
        function save() {
            $.ajax({
                type: 'POST',
                url: "{{ route('penpos.investasi.save') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'investation_id': $('#investasi_id').val(),
                    'nilai_investasi': $('#nilai_investasi').val(),
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