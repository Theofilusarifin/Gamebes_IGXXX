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
                <h2 class="fs-4 fw-bold mb-0" id="team_level">Level {{ Auth::user()->team->level }}</h2>
            </div>
            <div class="card-body">
                @include('peserta.layouts.alerts')
                <div class="row">
                    <div class="col-12 col-sm-12 col-xl-12 mt-1">
                        {{-- Logic BADGE --}}
                        @php($nama_syarat = ['Tingkat Efektifitas', 'Tingkat Higenis', 'Saldo Akhir'])
                        @php($success_count=0)
                        @for ($i = 1; $i <= 3; $i++) @php($class_badge='danger' ) @if ($i==1 && $team_level->syarat_1)
                            @php($class_badge = 'success')
                            @php($success_count+=1)
                            @elseif ($i == 2 && $team_level->syarat_2)
                            @php($class_badge = 'success')
                            @php($success_count+=1)
                            @elseif ($i == 3 && $team_level->syarat_3)
                            @php($class_badge = 'success')
                            @php($success_count+=1)
                            @endif
                            <span class="badge bg-{{$class_badge}} p-1" style="width: 150px;"
                                id="badge_syarat_{{$i}}">{{$nama_syarat[$i-1]}}</span>
                            @endfor
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 my-4">
                        <img style="object-fit: fill;"
                            src="{{ asset('/assets/img/level/'.Auth::user()->team->level.'/'.$success_count.'.png')}}"
                            alt="" id="gambar_level">
                    </div>
                    <div class="col-12 col-sm-12 col-xl-12 d-flex justify-content-between">
                        @if (Auth::user()->team->level < 3) <button class="btn btn-info" type="button"
                            id="update_syarat" onclick="updateSyarat()">Update
                            Syarat</button>
                            @else
                            <button disabled class="btn btn-info" type="button" id="update_syarat">Level Sudah
                                Max</button>
                            @endif

                            @if (Auth::user()->team->level < 3) @if ($success_count==3) <button class="btn btn-success"
                                id="upgrade_level" type="button" onclick="upgradeLevel()">Upgrade
                                Level</button>
                                @else
                                <button disabled class="btn btn-success" id="upgrade_level" type="button"
                                    onclick="upgradeLevel()">Upgrade
                                    Level</button>
                                @endif
                                @else
                                <button disabled class="btn btn-success" id="upgrade_level" type="button">Level Sudah
                                    Max</button>
                                @endif
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
                    // UBAH GAMBAR
                    var success_count = data.team_level.syarat_1*1 + data.team_level.syarat_2*1 + data.team_level.syarat_3*1;
                    // alert(success_count);
                    $('#gambar_level').attr("src",'https://penyisihan.igubaya.com/assets/img/level/'+ data.team.level +'/'+success_count+'.png');
                    
                    // UPDATE BADGE SYARAT 1
                    var class_1 = 'danger';
                    if (data.team_level.syarat_1*1){
                        class_1 = 'success';   
                    }
                    $('#badge_syarat_1').attr("class", 'badge bg-'+class_1+' p-1');
                    
                    // UPDATE BADGE SYARAT 2
                    var class_2 = 'danger';
                    if (data.team_level.syarat_2*1){
                        class_2 = 'success';   
                    }
                    $('#badge_syarat_2').attr("class", 'badge bg-'+class_2+' p-1');

                    // UPDATE BADGE SYARAT 3
                    var class_3 = 'danger';
                    if (data.team_level.syarat_3*1){
                        class_3 = 'success';   
                    }
                    $('#badge_syarat_3').attr("class", 'badge bg-'+class_3+' p-1');

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
                    if(data.team_level.syarat_1*1 && data.team_level.syarat_2*1 && data.team_level.syarat_3*1)
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
                $('#team_level').html('Level '+data.team_level.pivot.level_id);
                
                // UBAH GAMBAR
                $('#gambar_level').attr("src",'https://penyisihan.igubaya.com/assets/img/level/'+ data.team.level +'/0.png');

                    // UPDATE BADGE SYARAT 1
                    var class_1 = 'danger';
                    if (data.team_level.syarat_1*1){
                        class_1 = 'success';   
                    }
                    $('#badge_syarat_1').attr("class", 'badge bg-'+class_1+' p-1');
                    
                    // UPDATE BADGE SYARAT 2
                    var class_2 = 'danger';
                    if (data.team_level.syarat_2*1){
                        class_2 = 'success';   
                    }
                    $('#badge_syarat_2').attr("class", 'badge bg-'+class_2+' p-1');

                    // UPDATE BADGE SYARAT 3
                    var class_3 = 'danger';
                    if (data.team_level.syarat_3*1){
                        class_3 = 'success';   
                    }
                    $('#badge_syarat_3').attr("class", 'badge bg-'+class_3+' p-1');
                    
                    // Matikan button upgrade level
                    $('#upgrade_level').attr('disabled', true);   
                    
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
                            $('#upgrade_level').attr('disabled', true);
                        }
                        else if (data.status == "error") {
                            $('#alert').removeClass("alert-success");
                            $('#alert').addClass("alert-danger");
                            $('#upgrade_level').attr('disabled', false);
                        }
                    }
                }
            });
        }
</script>
@endsection