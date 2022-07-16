@extends('peserta.layouts.app')
@section('style')
<style>
    .btn {
        color: #fff !important;
    }

    .card-header {
        background-color: #1F2937 !important;
    }

    .card-header h2 {
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
                        {{-- Logic BADGE --}}
                        @php($nama_syarat = ['Tingkat Efektifitas', 'Tingkat Higenis', 'Saldo Akhir', 'Limbah'])
                        @for ($i = 1; $i <= 4; $i++) @php($class_badge='danger' ) @if ($i==1 && $team_level->
                            syarat_1)@php($class_badge = 'success')
                            @elseif ($i == 2 && $team_level->syarat_2)@php($class_badge = 'success')
                            @elseif ($i == 3 && $team_level->syarat_3)@php($class_badge = 'success')
                            @elseif ($i == 4 && $team_level->syarat_4)@php($class_badge = 'success')
                            @endif
                            <span class="badge bg-{{$class_badge}} p-1" style="width: 150px;"
                                id="badge_syarat_{{$i}}">{{$nama_syarat[$i-1]}}</span>
                            @endfor
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 my-4">
                        <img style="object-fit: fill;" src="{{ asset('/assets/img/background/map_background.png')}}"
                            alt="">
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 d-flex justify-content-between">
                        <button class="btn btn-info" type="button" id="update_syarat" onclick="updateSyarat()">Update
                            Syarat</button>
                        <button disabled class="btn btn-success" id="upgrade_level" type="button"
                            onclick="upgradeLevel()">Upgrade
                            Level</button>
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
            $('#update_syarat').attr('disabled', true);
            $.ajax({
                type: 'POST',
                url: "{{ route('peserta.level.update') }}",
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (data) {
                    // UPDATE BADGE SYARAT 1
                    var class_1 = 'danger';
                    if (data.team_level.syarat_1){
                        class_1 = 'success';   
                    }
                    $('#badge_syarat_1').attr("class", 'badge bg-'+class_1+' p-1');
                    
                    // UPDATE BADGE SYARAT 2
                    var class_2 = 'danger';
                    if (data.team_level.syarat_2){
                        class_2 = 'success';   
                    }
                    $('#badge_syarat_2').attr("class", 'badge bg-'+class_2+' p-1');

                    // UPDATE BADGE SYARAT 3
                    var class_3 = 'danger';
                    if (data.team_level.syarat_3){
                        class_3 = 'success';   
                    }
                    $('#badge_syarat_3').attr("class", 'badge bg-'+class_3+' p-1');
                    
                    // UPDATE BADGE SYARAT 4
                    var class_4 = 'danger';
                    if (data.team_level.syarat_4){
                        class_4 = 'success';   
                    }
                    $('#badge_syarat_4').attr("class", 'badge bg-'+class_4+' p-1');

                    // UBAH GAMBAR
                    
                    //Tampilin Alert Message
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

                    $('#update_syarat').attr('disabled', false);
                    //Enable Upgrade Level
                    if(data.team_level.syarat_1 && data.team_level.syarat_2 && data.team_level.syarat_3 && data.team_level.syarat_4)
                    {
                        $('#upgrade_level').attr('disabled', false);            
                    }
                }
            });
        }

        function upgradeLevel() {
            $('#upgrade_level').attr('disabled', true);
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

                    $('#upgrade_level').attr('disabled', false);
                }
            });
        }
</script>
@endsection