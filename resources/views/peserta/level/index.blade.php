@extends('peserta.layouts.app')
@section('style')
<style>
    .btn {
        color: #fff !important;
    }

    .card-header {
        background-color: #1F2937 !important;
    }

    .card-header h2{
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<main class="row px-5">
    <div class="row my-5 ps-4">
        <div class="card border-light shadow-sm p-0 m-0">
            <div class="card-header border-bottom d-flex align-items-center">
                <h2 class="fs-4 fw-bold mb-0">Level {{ Auth::user()->team->level }}</h2>
            </div>
            <div class="card-body">
                @include('peserta.layouts.alerts')
                <div class="row">
                    <div class="col-12 col-sm-12 col-xl-12 mt-1">
                        <span class="badge bg-success p-1" style="width: 150px;" id="badge_syarat_1">Tingkat Efektifitas</span>
                        <span class="badge bg-danger p-1" style="width: 150px;" id="badge_syarat_2">TIngkat Higenis</span>
                        <span class="badge bg-danger p-1" style="width: 150px;" id="badge_syarat_3">Saldo Akhir</span>
                        <span class="badge bg-danger p-1" style="width: 150px;" id="badge_syarat_4">Limbah</span>
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 my-4">
                        <img style="object-fit: fill;" src="{{ asset('/assets/img/background/map_background.png')}}" alt="">
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 d-flex justify-content-between">
                        <button class="btn btn-info" type="button" onclick="updateSyarat()">Update Syarat</button>
                        <button disabled class="btn btn-success" type="button" onclick="upgradeLevel()">Upgrade Level</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
    function updateSyarat() {
            $.ajax({
                type: 'POST',
                url: "{{ route('peserta.level.update') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (data) {
                    alert(data.team_level.syarat_1);

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

        function upgradeLevel() {
            $.ajax({
                type: 'POST',
                url: "{{ route('peserta.level.upgrade') }}",
                data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
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