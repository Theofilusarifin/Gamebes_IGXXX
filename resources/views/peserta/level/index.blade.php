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
<br>
<span class="badge bg-success p-2" id="badge_syarat_1">Tingkat Efektifitas</span>
<span class="badge bg-danger p-2" id="badge_syarat_2">TIngkat Higenis</span>
<span class="badge bg-danger p-2" id="badge_syarat_3">Saldo Akhir</span>
<span class="badge bg-danger p-2" id="badge_syarat_4">Limbah</span>
<br>
<br><br>
<button class="btn btn-info" type="button" onclick="updateSyarat()">Update Syarat</button>
<button class="btn btn-success" type="button" onclick="upgradeLevel()">Upgrade Level</button>
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