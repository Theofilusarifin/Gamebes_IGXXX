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
<main class="row px-5">
    <div class="row my-5">
        <div class="col-12 col-sm-6 col-xl-6">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="row d-flex align-items-center">
                        {{-- Judul --}}
                        <div class="col-6">
                            <h1 class="fs-5 fw-bold text-white mb-0">Update Season</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Alert --}}
                    @include('penpos.layouts.alerts')
                    <div class="row">
                        <h5 class="text-danger mb-3">Jangan ditekan kalau ga berkepentingan! 😠</h5>
                        <div class="col-12 d-flex justify-content-center mt-2">
                            <button class="btn btn-success" id="update_season" type="button" style="width: 600px"
                                onclick="updateSeason()">Update Season</button>
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
    function updateSeason() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.update.now') }}",
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