@extends('penpos.layouts.map')

@section('style')
<style>
    td {
    min-width: 5px;
    min-height: 5px;
    width: 30px;
    height: 30px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    /* border: 1px; */
    }

    .water{
        width: 10px;
        height: 15px;
        background-color: #8DB5F8
    }
    .wall{
        background-color: #1f2937
    }
    .harbour{
        width: 30px;
        /* background-color: #EA4335; */
        background-color: rgba(223, 169, 70, 0.5);
        cursor: pointer;
    }
    .harbour:hover {
        background-color: rgba(223, 169, 70, 0.2);
    }
    .company{
        background-color: #1f2937;
        /* border: 1px dashed rgb(84, 84, 84); */
        height: 5px;
        width: 0px;
    }

    .machine_store{
        background-color: #10B981;
        color: #FFF;
    }

    .ingridient_store{
        background-color: #f0bc74;
    }

    .transport_store{
        background-color: #E11D48;  
        color: #FFF;
    }

    .service{
        background-color: grey;
        color: #FFF;
    }

    td:not(.water,.company){
        border: 1px dashed rgb(84, 84, 84);
    }

    .dot {
        height: 28px;
        width: 28px;
        background-color: #A47551;
        color: #fff;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="row my-2 d-flex mx-2">
    {{-- Map --}}
    <div class="col-9">
        @php($column = 60)
        <table id="mainTable" class="m-4">
            @foreach ($territories as $territory)
                @php($alias = "")
                {{-- Buka Tr --}}
                @if($territory->open_tr)
                    <tr>
                @endif

                {{-- Inisialisasi Class --}}
                @php($class="")
                @php($onclick=false)

                @if ($territory->is_wall) @php($class="wall")
                @elseif ($territory->is_water) @php($class="water")
                @elseif ($territory->is_harbour) 
                    @php($class="harbour")
                    @php($onclick=true)
                @elseif ($territory->is_company) @php($class="company")
                
                {{-- Store --}}
                @elseif (isset($territory->transport_store_id)) 
                    @php($class= "transport_store")
                    @php($alias = $territory->transport_store_id)
                @elseif (isset($territory->ingridient_store_id))
                    @php($class= "ingridient_store")
                    @php($alias = $territory->ingridient_store_id)
                @elseif (isset($territory->machine_store_id))
                    @php($class= "machine_store")
                    @php($alias = $territory->machine_store_id)  
                @elseif (isset($territory->service_id))
                    @php($class= "service")
                    @php($alias = $territory->service_id)
                @endif

                {{-- Buat Td --}}
                @if($onclick)
                    <td class="{{ $class }}" id="{{ $territory->id }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}" onclick="setSpawnPoint({{ $territory->id }})">
                        @if($territory->num_occupant > 0)
                            <div class="dot">{{ $territory->teams->first()->id }}</div>
                            @php($alias = "")
                        @endif
                        {{ $alias }}
                    </td>
                @else
                    <td class="{{ $class }}" num_occupants="{{ $territory->num_occupant }}" id="{{ $territory->id }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}" onclick="{{ $onclick }}">
                        @if($territory->num_occupant > 0)
                            <div class="dot">{{ $territory->teams->first()->id }}</div>
                            @php($alias = "")
                        @endif
                        {{ $alias }}
                    </td>
                @endif
                
                {{-- Nutup tr --}}
                @if($territory->close_tr)
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
    <div class="col-3">
        <div class="card card-body border-0 shadow mt-4 me-5" style="background-color:#1f2937;">
            <div class="card-body">
                {{-- Alert --}}
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show" id="alert" style="display:none" role="alert">
                            <span class="fas fa-bullhorn me-1" id="alert-body"></span>
                        </div>
                    </div>
                </div>
                {{-- Team --}}
                <p class="text-white">Nama Team</p>

                <select class="select2 form-select" id="team_id" tabindex="-1" aria-hidden="true">
                    <option selected disabled>-- Pilih Nama Team --</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}">
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Button Timer --}}
                <div class="d-flex justify-content-center px-4 mt-4 mb-1">
                    <h5 class="text-white"><b>Timer : <span id="timer">120</span></b></h5>
                </div>
                <div class="d-flex justify-content-center px-3">
                    <div class="btn-group">
                        <button class="btn btn-success" type="button" id="start">Start</button>
                        <button class="btn btn-warning" type="button" id="pause">Pause</button>
                        <button class="btn btn-danger" type="button" id="reset">Reset</button>
                    </div>
                </div>

                {{-- Button Move --}} 
                <div class="d-flex justify-content-center px-4 mt-4 mb-1">
                    <h5 class="text-white"><b>Capacity : <span id="capacity">0</span></b></h5>
                </div>
                <div class="d-flex justify-content-center px-4 mt-2">
                    <button type="button" class="btn btn-gray-50 me-3 mb-3 " onclick="move('kanan_atas')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-up-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 mb-3 " onclick="move('atas')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-up' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 mb-3 " onclick="move('kiri_atas')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-up-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-center px-4" >
                    <button type="button" class="btn btn-gray-50 me-3 " onclick="move('kiri')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-gray-500 " onclick="action()" style="width: 58px; height:58px;">
                        <i data-feather='zap' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 " onclick="move('kanan')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-center px-4" >
                    <button type="button" class="btn btn-gray-50 me-3 mt-3 " onclick="move('kiri_bawah')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-down-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 mt-3 " onclick="move('bawah')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-down' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 mt-3 " onclick="move('kanan_bawah')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-down-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>

                {{-- Reset capacity Button --}}
                <div class="d-flex flex-row-reverse">
                    <button type="button" class="btn btn-icon btn-info" id ="reset_capacity" style="width: 48px; height:48px;" >
                        <i data-feather='rotate-cw' style="width: 18px; height:18px;"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    function setSpawnPoint(territory_id) {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.spawn') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'territory_id': territory_id,
                },
            success: function (data) {
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
        })
    }

    function move(arah) {
        $('.btn-gray-50').attr('disabled', true);

        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.move') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'arah': arah,
                    'team_id': $('#team_id').val(),
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
            $('.btn-gray-50').attr('disabled', false);
            }
        })
    }

    function action() {
        $('.btn-control-action').attr('disabled', true);
        
        // Ambil remaining capacity
        var remaining_capacity = $("#capacity").text() * 1;

        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.action') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'sisa_move': remaining_capacity,
                },
            success: function (data) {
                // Jika response error, tidak usah di kurangi Actionnya
                if(data.response == 'error') {
                    $('.btn-control-action').attr('disabled', false);
                } else {
                    // Kurangi remaining capacity
                    if (remaining_capacity > 0) {
                        remaining_capacity -= 1;
                        $("#capacity").html(remaining_capacity);
                    }
                }

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
                if (data.status2 != ""){
                    $('#alert-2').hide();
                    $('#alert-2').show();
                    $('#alert-body-2').html(data.msg2);

                    $("#alert-2").fadeTo(5000, 500).hide(1000, function(){
                            $("#alert-2").hide(1000);
                        });
                    if (data.status2 == "success") {
                        $('#alert-2').removeClass("alert-danger");
                        $('#alert-2').addClass("alert-success");
                    } 
                    else if (data.status2 == "error") {
                        $('#alert-2').removeClass("alert-success");
                        $('#alert-2').addClass("alert-danger");
                    }
                }
            }
        })
    }
</script>
<script>
    var timer;
    var second = 120;
    var running = false;
    $(document).on('click', '#start', function() {
        if (!running){
            $("#timer").css('display','inline');
            running = true;
            timer = setInterval(function() {
                second--;
                $("#timer").text(second);
                if (second <= 0) {
                    $("#timer").text('Waktu Habis');
                    alert('Waktu Habis');
                    running = false;
                    clearInterval(timer);
                    second = 120;
                }
            }, 1000);
        }
    });

    
    $(document).on('click','#pause', function(){
        $("#timer").text(second);
        running = false
        clearInterval(timer);
    });
    
    $(document).on('click','#reset', function(){
        $("#timer").text('120');
        running = false;
        clearInterval(timer);
        second = 120;
    });

</script>
@endsection