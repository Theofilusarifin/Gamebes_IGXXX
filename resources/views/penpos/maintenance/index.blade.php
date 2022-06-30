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
{{-- Card Maintenance --}}
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Maintenance</h1>
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
                                <select class="form-select" id="team_id" aria-label="Default select example"
                                    onchange="getTeamMachines()">
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
                        {{-- Pilih Mesin --}}
                        <div class="col-7">
                            <div class="mb-4">
                                <label class="my-1 me-2" for="team_machine_id">Pilih Mesin</label>
                                <select disabled class="form-select" id="team_machine_id"
                                    aria-label="Default select example">
                                    <option selected disabled>-- Pilih Team Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                        {{-- Jumlah Barang --}}
                        <div class="col-5">
                            <label class="my-1 me-2" for="nilai_maintenance">Persentase Maintenance (%)</label>
                            <select class="form-select" id="nilai_maintenance">
                                <option value="25">25%</option>
                                <option value="50">50%</option>
                                <option value="75">75%</option>
                                <option value="100">100%</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-success" id="save_saus" style="width: 100px" type="button"
                                onclick="maintenance()">Submit</button>
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
    function getTeamMachines() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.maintenance.get.machine') }}",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'team_id': $('#team_id').val()
            },
            success: function(data) {
                var option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
                $.each(data.team_machines, (key, team_machine) => {
                    option_variable += `<option value=${team_machine.id}>${team_machine.id}-${team_machine.machine.name}
                    (${team_machine.performance})</option>`;
                });
                $('#team_machine_id').attr('disabled', false)
                $('#team_machine_id').html(option_variable);
            }
        });
    }

    function maintenance() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.maintenance.save') }}",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'team_id': $('#team_id').val(),
                'team_machine_id': $('#team_machine_id').val(),
                'nilai_maintenance': $('#nilai_maintenance').val(),
            },
            success: function(data) {
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