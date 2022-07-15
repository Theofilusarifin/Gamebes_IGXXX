@extends('penpos.layouts.map')

@section('style')
<style>
    #mainTable {
        background-image: url(../../../assets/img/background/map_background.png);
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        width: 100%;
        font-size: 0.6em;
    }

    td {
        min-width: 30px;
        min-height: 30px;
        width: 30px;
        height: 30px;
        max-width: 30px;
        max-height: 30px;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        /* border: 1px; */
    }

    .water {
        width: 30px;
        height: 30px;
        background-color: #8DB5F8
    }

    .wall {
        background-color: #1f2937
    }

    .harbour {
        width: 30px;
        /* background-color: #EA4335; */
        background-color: rgb(245, 197, 108);
        cursor: pointer;
        font-weight: 800;
    }

    .harbour:hover {
        background-color: rgba(223, 169, 70, 0.6);
    }

    .company {
        background-color: #e3f821;
        width: 30px;
    }

    .home {
        background-color: #5bac66;
        width: 30px;
    }

    .machine_store {
        background-color: #10B981;
        color: #FFF;
    }

    .ingridient_store {
        background-color: #645aeb;
        color: #FFF;
    }

    .transport_store {
        background-color: #E11D48;
        color: #FFF;
    }

    .service {
        background-color: grey;
        color: #FFF;
    }

    td:not(.water, .company, .empty, .home) {
        border: 1px dashed rgb(84, 84, 84);
    }

    td:not(.water, .company, .empty, .home, .harbour):hover {
        background-color: rgba(212, 206, 206, 0.6);
    }

    .dot {
        height: 28px;
        width: 28px;
        background-color: #fff;
        color: #000000;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .position_dot_1 {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .position_dot_2 {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
    }

    .dot_1 {
        height: 15px;
        width: 15px;
        background-color: #fff;
        color: #000000;
        border-radius: 50%;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dot_2 {
        height: 15px;
        width: 15px;
        background-color: #fff;
        color: #000000;
        border-radius: 50%;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .col-1 {
        width: 3%;
    }

    .col-10 {
        width: 86%;
    }

    .col-9 {
        width: 78%;
    }

    .col-3 {
        width: 22%;
    }
</style>
@endsection

@section('content')
<div class="row mx-4">
    <div class="col mx-2">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="map_view" onchange="change_view()">
            <label class="form-check-label" for="map_view">Map View</label>
        </div>
    </div>
</div>
<div class="row my-3 d-flex">
    {{-- Map --}}
    <div class="col-9" id="col_map">
        <div class="row">
            <div class="col-1 pt-3 ms-5 mt-5 me-3">
                <table id="mainTableLeft" class="my-4">
                    @foreach ($left_companies as $left_company)
                    <tr>
                        {{-- Tentukan Class --}}
                        @php($class="empty")
                        @if ($left_company->is_company) @php($class="company")@endif
                        @if ($left_company->is_home) @php($class="home")@endif

                        @if($left_company->num_occupant > 0)
                        <td class="{{ $class }}" id="{{ $left_company->id }}">
                            <div class="dot">{{ $left_company->teams->first()->id }}</div>
                        </td>
                        @else
                        <td class="{{ $class }}" id="{{ $left_company->id }}"></td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="col-10">
                <div>
                    <table id="mainTableUpper">
                        <tr>
                            @foreach ($upper_companies as $upper_company)
                            {{-- Tentukan Class --}}
                            @php($class="empty")
                            @if ($upper_company->is_company) @php($class="company")@endif
                            @if ($upper_company->is_home) @php($class="home")@endif

                            @if($upper_company->num_occupant > 0)
                            <td class="{{ $class }}" id="{{ $upper_company->id }}">
                                <div class="dot">{{ $upper_company->teams->first()->id }}</div>
                            </td>
                            @else
                            <td class="{{ $class }}" id="{{ $upper_company->id }}"></td>
                            @endif
                            @endforeach
                        </tr>
                    </table>

                    @php($column = 42)
                    @php($index_pelabuhan = 1)
                    <table id="mainTable" class="my-4">
                        @foreach ($territories as $territory)
                        {{-- Buka Tr --}}
                        @if ($loop->index == 0 || $loop->index % $column == 0)@php($dibuka = $loop->index)<tr>@endif

                            {{-- Inisialisasi Class --}}
                            @php($alias = "")
                            @php($class="")
                            @php($onclick=false)

                            @if ($territory->is_wall) @php($class="wall")
                            @elseif ($territory->is_water) @php($class="water")
                            @elseif ($territory->is_harbour)
                            @php($class="harbour")
                            @php($onclick=true)
                            {{-- Kasik nama ke pelabuhan --}}
                            @php($alias = "P".$index_pelabuhan)
                            @php($index_pelabuhan+=1)
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
                            <td class="{{ $class }}" id="{{ $territory->id }}"
                                onclick="setSpawnPoint({{ $territory->id }})">
                                @if($territory->num_occupant > 0)
                                <div class="dot">{{ $territory->teams->first()->id }}</div>
                                @php($alias = "")
                                @endif
                                {{ $alias }}
                            </td>
                            @else
                            <td class="{{ $class }}" num_occupants="{{ $territory->num_occupant }}"
                                id="{{ $territory->id }}">
                                @if($territory->num_occupant == 1)
                                @php($alias = "")
                                <div class="dot">{{ $territory->teams->first()->id }}</div>
                                @elseif($territory->num_occupant == 2)
                                @php($alias = "")
                                <div class="position_dot_1">
                                    <div class="dot_1">
                                        {{ $territory->teams->all()[0]->id }}
                                    </div>
                                </div>
                                <div class="position_dot_2">
                                    <div class="dot_2">
                                        {{ $territory->teams->all()[1]->id }}
                                    </div>
                                </div>
                                @endif
                                {{ $alias }}
                            </td>
                            @endif

                            {{-- Nutup tr --}}
                            @if($loop->index == $dibuka + $column)
                        </tr>@endif
                        @endforeach
                    </table>

                    <table id="mainTableLower">
                        <tr>
                            @foreach ($lower_companies as $lower_company)
                            {{-- Tentukan Class --}}
                            @php($class="empty")
                            @if ($lower_company->is_company) @php($class="company")@endif
                            @if ($lower_company->is_home) @php($class="home")@endif

                            @if($lower_company->num_occupant > 0)
                            <td class="{{ $class }}" id="{{ $lower_company->id }}">
                                <div class="dot">{{ $lower_company->teams->first()->id }}</div>
                            </td>
                            @else
                            <td class="{{ $class }}" id="{{ $lower_company->id }}"></td>
                            @endif
                            @endforeach
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-1 pt-3 mt-5 ms-3">
                <table id="mainTableRight" class="my-4">
                    @foreach ($right_companies as $right_company)
                    <tr>
                        {{-- Tentukan Class --}}
                        @php($class="empty")
                        @if ($right_company->is_company) @php($class="company")@endif
                        @if ($right_company->is_home) @php($class="home")@endif

                        @if($right_company->num_occupant > 0)
                        <td class="{{ $class }}" id="{{ $right_company->id }}">
                            <div class="dot">{{ $right_company->teams->first()->id }}</div>
                        </td>
                        @else
                        <td class="{{ $class }}" id="{{ $right_company->id }}"></td>
                        @endif
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    {{-- Controller --}}
    <div class="col-3" id="col_controller">
        <div class="card card-body border-0 shadow mt-4 me-5" style="background-color:#1f2937;">
            <div class="card-body">
                {{-- Alert --}}
                @include('penpos.layouts.alerts')
                {{-- Team --}}
                <p class="text-white">Nama Team</p>

                <select class="select2 form-select" id="team_id" tabindex="-1" aria-hidden="true"
                    onchange="getCapacity()">
                    <option selected disabled>-- Pilih Nama Team --</option>
                    @foreach ($teams as $team)
                    <option value="{{ $team->id }}">
                        {{ $team->name }}
                    </option>
                    @endforeach
                </select>

                {{-- Button Timer --}}
                <div class="d-flex justify-content-center px-4 mt-4 mb-1">
                    <h5 class="text-white"><b>Timer : <span id="timer">60</span></b></h5>
                </div>
                <div class="d-flex justify-content-center px-3 mb-4">
                    <div class="btn-group">
                        <button class="btn btn-success" type="button" id="start">Start</button>
                        <button class="btn btn-warning" type="button" id="pause">Pause</button>
                        <button class="btn btn-danger" type="button" id="reset">Reset</button>
                    </div>
                </div>

                {{-- Button Timer 2 --}}
                <div class="d-flex justify-content-center px-4 mt-4 mb-1">
                    <h5 class="text-white"><b>Timer : <span id="timer_2">30</span></b></h5>
                </div>
                <div class="d-flex justify-content-center px-3 mb-4">
                    <div class="btn-group">
                        <button class="btn btn-success" type="button" id="start_2">Start</button>
                        <button class="btn btn-warning" type="button" id="pause_2">Pause</button>
                        <button class="btn btn-danger" type="button" id="reset_2">Reset</button>
                    </div>
                </div>

                {{-- Capacity --}}
                <div class="d-flex justify-content-center px-4 mt-4 mb-1">
                    <h5 class="text-white"><b>Capacity : <span id="capacity">0</span></b></h5>
                </div>

                {{-- Button Move --}}
                <div class="d-flex justify-content-center px-4 mt-2">
                    <button type="button" class="btn btn-gray-50 me-3 mb-3 btn-control-action"
                        onclick="move('kanan_atas')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-up-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 mb-3 btn-control-action" onclick="move('atas')"
                        style="width: 58px; height:58px;">
                        <i data-feather='arrow-up' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 mb-3 btn-control-action"
                        onclick="move('kiri_atas')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-up-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-center px-4">
                    <button type="button" class="btn btn-gray-50 me-3 btn-control-action" onclick="move('kiri')"
                        style="width: 58px; height:58px;">
                        <i data-feather='arrow-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-gray-500 btn-control-action" onclick="action()"
                        style="width: 58px; height:58px;">
                        <i data-feather='zap' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 btn-control-action" onclick="move('kanan')"
                        style="width: 58px; height:58px;">
                        <i data-feather='arrow-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-center px-4">
                    <button type="button" class="btn btn-gray-50 me-3 mt-3 btn-control-action"
                        onclick="move('kiri_bawah')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-down-left' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 mt-3 btn-control-action" onclick="move('bawah')"
                        style="width: 58px; height:58px;">
                        <i data-feather='arrow-down' style="width: 24px; height:24px;"></i>
                    </button>
                    <button type="button" class="btn btn-gray-50 ms-3 mt-3 btn-control-action"
                        onclick="move('kanan_bawah')" style="width: 58px; height:58px;">
                        <i data-feather='arrow-down-right' style="width: 24px; height:24px;"></i>
                    </button>
                </div>

                {{-- Button Back To Company --}}
                <div class="d-flex flex-row-reverse mt-4">
                    <button type="button" class="btn btn-icon btn-info" id="reset_capacity"
                        style="width: 48px; height:48px;" onclick="backToCompany()">
                        <i data-feather='home' style="width: 18px; height:18px;"></i>
                    </button>
                </div>

                {{-- Team --}}
                <p class="text-white mt-4" id="nama_store">Nama Store</p>

                <select class="select2 form-select mb-4" id="nama_item" tabindex="-1" aria-hidden="true" disabled>
                    <option selected disabled>-- Pilih Item --</option>
                </select>

                {{-- Tempat Beli --}}
                <div class="row d-flex justify-content-between" id="buy_section">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="../js/app.js"></script>

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
        $('.btn-control-action').attr('disabled', true);
        $('#nama_item').attr('disabled', true);
        $('#nama_item').html(`<option selected disabled>-- Pilih Item --</option>`);
        $('#nama_store').html("Nama Toko");
        $('#buy_section').html('');

        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;

        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.move') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'arah': arah,
                    'current_capacity': current_capacity,
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
                        $('.btn-control-action').attr('disabled', false);
                    }
                }
            }
        })
    }

    function action() {
        $('.btn-control-action').attr('disabled', true);
        
        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;

        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.action') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'current_capacity': current_capacity,
                },
            success: function (data) {
                if(data.response == 'error') {
                    $('.btn-control-action').attr('disabled', false);
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

                        $('#nama_store').html("Toko " + data.store.name);

                        if (data.store.name == 'Jasa Pembersih'){
                            $('#nama_item').attr('disabled', false);
                            $option_item = "";
                            $option_item +=
                            `<option value=" ${data.store.id} ">
                                ${data.store.name} - (${data.store.stock})
                            </option>`
                            $('#nama_item').append($option_item);
                            $('#buy_section').html(`
                            <input class="form-control" type="hidden" id='banyak_item' value="1">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-icon btn-success" style="color:white" id="btn_buy_item"
                                    onclick="buy${data.type}('${data.store.id}')">
                                    Buy
                                </button>
                            </div>`
                            );
                        }
                        else{
                            $option_item = "";
                            if (data.store_items != null){
                                $('#nama_item').attr('disabled', false);
                                $.each(data.store_items, (key, store_item) => {
                                    $option_item += 
                                    `<option value=" ${store_item.id} ">
                                        ${store_item.name} - (${store_item.pivot.stock})
                                    </option>`
                                });
                                $('#nama_item').append($option_item);
                                $('#buy_section').html(`
                                    <div class="col-9">
                                        <div class="form-group">
                                            <input class="form-control" type="number" min=0 placeholder="-- Banyak Barang --" id='banyak_item' required="">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <button type="button" class="btn btn-icon btn-success" style="color:white" id="btn_buy_item" onclick="buy${data.type}('${data.store.id}')">
                                            Buy
                                        </button>
                                    </div>`
                                );
                            }
                        }
                    }
                    else if (data.status == "error") {
                        $('#alert').removeClass("alert-success");
                        $('#alert').addClass("alert-danger");
                        $('.btn-control-action').attr('disabled', false);
                    }
                }
            }
        });
    }

    function buyTransport(store_id) {
        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.buy.transport') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'item_id': $('#nama_item').val(),
                    'store_id': store_id,
                    'banyak_item': $('#banyak_item').val(),
                    'current_capacity': current_capacity,
                },
            success: function (data) {
                // Jalankan funtion get cpaacity untuk melihat capacity baru yang sudah ditambah
                getCapacity();
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

    function buyIngridient(store_id) {
        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.buy.ingridient') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'item_id': $('#nama_item').val(),
                    'store_id': store_id,
                    'banyak_item': $('#banyak_item').val(),
                    'current_capacity': current_capacity,
                },
            success: function (data) {
                // Jalankan funtion get cpaacity untuk melihat capacity baru yang sudah ditambah
                getCapacity();
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

    function buyMachine(store_id) {
        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.buy.machine') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'item_id': $('#nama_item').val(),
                    'store_id': store_id,
                    'banyak_item': $('#banyak_item').val(),
                    'current_capacity': current_capacity,
                },
            success: function (data) {
                // Jalankan funtion get cpaacity untuk melihat capacity baru yang sudah ditambah
                getCapacity();
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

    function buyService(store_id) {
        // Ambil current capacity
        var current_capacity = $("#capacity").text() * 1;
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.buy.service') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                    'item_id': $('#nama_item').val(),
                    'store_id': store_id,
                    'banyak_item': $('#banyak_item').val(),
                    'current_capacity': current_capacity,
                },
            success: function (data) {
                // Jalankan funtion get cpaacity untuk melihat capacity baru yang sudah ditambah
                getCapacity();
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
<script>
    function change_view() {
        status_check = $('#map_view').is(":checked");
        if (status_check){
            $('#col_map').attr('class', 'col-12')
            $('#col_map').addClass('d-flex')
            $('#col_map').addClass('justify-content-center')
            $('#col_controller').attr('class', '')
            $('#col_controller').css('display', 'none')
        }
        else{
            $('#col_map').attr('class', 'col-9')
            $('#col_map').removeClass('d-flex')
            $('#col_map').removeClass('justify-content-center')
            $('#col_controller').attr('class', 'col-3')
            $('#col_controller').css('display', 'flex')
        }
    }
</script>
<script>
    var timer;
    var second = 60;
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
                    second = 60;
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
        $("#timer").text('60');
        running = false;
        clearInterval(timer);
        second = 60;
    });

</script>

<script>
    var timer_2;
    var second_2 = 30;
    var running_2 = false;
    $(document).on('click', '#start_2', function() {
        if (!running_2){
            $("#timer_2").css('display','inline');
            running_2 = true;
            timer_2 = setInterval(function() {
                second_2--;
                $("#timer_2").text(second_2);
                if (second_2 <= 0) {
                    $("#timer_2").text('Waktu Habis');
                    alert('Waktu Habis');
                    running_2 = false;
                    clearInterval(timer_2);
                    second_2 = 30;
                }
            }, 1000);
        }
    });

    
    $(document).on('click','#pause_2', function(){
        $("#timer_2").text(second_2);
        running_2 = false
        clearInterval(timer_2);
    });
    
    $(document).on('click','#reset_2', function(){
        $("#timer_2").text('30');
        running_2 = false;
        clearInterval(timer_2);
        second_2 = 30;
    });

    function getCapacity() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.get.capacity') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'team_id': $('#team_id').val(),
                },
            success: function (data) {
                if (data.status == "success") {
                    $('#capacity').html(data.team.current_capacity);
                } 
            }
        });
    }

    function backToCompany() {
        $.ajax({
            type: 'POST',
            url: "{{ route('penpos.map.back.to.company') }}",
            data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
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
                        $('.btn-control-action').attr('disabled', false);
                    }
                }
            }
        });
    }

</script>

<script>
    window.Echo.channel("update-map").listen(".UpdateMapMessage", (e) => {
        let tableDataLeft = "";
        let tableDataUpper = "";
        let tableData = "";
        let tableDataLower = "";
        let tableDataRight = "";

        $.ajax({
            type: "POST",
            url: "/map/update-map",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                // START TABLE LEFT
                $.each(data.left_companies, (key, left_company) => {
                    tableDataLeft += `<tr>`;
                    // Tentukan Class
                    let classLeft = "empty";
                    if (left_company.is_company == 1) {
                        classLeft = "company";
                    }
                    if (left_company.is_home == 1) {
                        classLeft = "home";
                    }

                    if (left_company.num_occupant > 0) {
                        tableDataLeft += `
                        <td class='${classLeft}' id='${left_company.id}'>
                            <div class="dot">${left_company.teams[0].id}</div>
                        </td>`;
                    } else {
                        tableDataLeft += `
                        <td class='${classLeft}' id='${left_company.id}'></td>`;
                    }
                    tableDataLeft += `</tr>`;
                });

                $("#mainTableLeft").html(tableDataLeft);
                // END TABLE LEFT

                // START TABLE UPPER
                tableDataUpper += `<tr>`;
                $.each(data.upper_companies, (key, upper_company) => {
                    let classUpper = "empty";
                    if (upper_company.is_company == 1) {
                        classUpper = "company";
                    }
                    if (upper_company.is_home == 1) {
                        classUpper = "home";
                    }

                    if (upper_company.num_occupant > 0) {
                        tableDataUpper += `<td class='${classUpper}' id='${upper_company.id}'>
                            <div class="dot">${upper_company.teams[0].id}</div>
                        </td>`;
                    } else {
                        tableDataUpper += `<td class='${classUpper}' id='${upper_company.id}'></td>`;
                    }
                });
                tableDataUpper += `</tr>`;

                $("#mainTableUpper").html(tableDataUpper);
                // END TABLE UPPER

                // START TABLE MAIN
                let column = 42;
                let index_pelabuhan = 1;
                let dibuka = "";

                $.each(data.territories, (key, territory) => {
                    // Buka TR
                    if (key == 0 || key % column == 0) {
                        dibuka = key;
                        tableData += `<tr>`;
                    }

                    // Inisiasi Class
                    let alias = "";
                    let classes = "";
                    let onclick = false;

                    if (territory.is_wall == 1) {
                        classes = "wall";
                    } else if (territory.is_water == 1) {
                        classes = "water";
                    } else if (territory.is_harbour == 1) {
                        classes = "harbour";
                        onclick = true;
                        alias = "P" + index_pelabuhan;
                        index_pelabuhan++;
                    } else if (territory.is_company == 1) {
                        classes = "company";
                    }
                    // Store
                    else if (territory.transport_store_id != null) {
                        classes = "transport_store";
                        alias = territory.transport_store_id;
                    } else if (territory.ingridient_store_id != null) {
                        classes = "ingridient_store";
                        alias = territory.ingridient_store_id;
                    } else if (territory.machine_store_id != null) {
                        classes = "machine_store";
                        alias = territory.machine_store_id;
                    } else if (territory.service_id != null) {
                        classes = "service";
                        alias = territory.service_id;
                    }

                    // Buat TD
                    if (onclick) {
                        tableData += `<td class='${classes}' id='${territory.id}' onclick="setSpawnPoint(${territory.id})">`;
                        if (territory.num_occupant > 0) {
                            tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                            alias = "";
                        }
                        tableData += ` ${alias} `;
                        tableData += `</td>`;
                    } else {
                        tableData += `<td class='${classes}' num_occupants='${territory.num_occupant}' id='${territory.id}'>`;
                        if (territory.num_occupant == 1) {
                            alias = "";
                            tableData += `<div class='dot'> ${territory.teams[0].id} </div>`;
                        } else if (territory.num_occupant == 2) {
                            alert("masuk num 2");
                            alias = "";
                            tableData += ` 
                            <div class="position_dot_1">
                                <div class="dot_1">
                                    ${territory.teams[0].id}
                                </div>
                            </div>
                            <div class="position_dot_2">
                                <div class="dot_2">
                                    ${territory.teams[1].id}
                                </div>
                            </div>
                            `;
                        }
                        tableData += ` ${alias} `;
                        tableData += `</td>`;
                    }

                    // Nutup TR
                    if (key == dibuka + column) {
                        tableData += `</tr>`;
                    }
                });

                $("#mainTable").html(tableData);
                // END TABLE MAIN

                // START TABLE LOWER
                tableDataLower += `<tr>`;
                $.each(data.lower_companies, (key, lower_company) => {
                    let classLower = "empty";
                    if (lower_company.is_company == 1) {
                        classLower = "company";
                    }
                    if (lower_company.is_home == 1) {
                        classLower = "home";
                    }

                    if (lower_company.num_occupant > 0) {
                        tableDataLower += `<td class='${classLower}' id='${lower_company.id}'>
                            <div class="dot">${lower_company.teams[0].id}</div>
                        </td>`;
                    } else {
                        tableDataLower += `<td class='${classLower}' id='${lower_company.id}'></td>`;
                    }
                });
                tableDataLower += `</tr>`;

                $("#mainTableLower").html(tableDataLower);
                // END TABLE LOWER

                // START TABLE RIGHT
                $.each(data.right_companies, (key, right_company) => {
                    tableDataRight += `<tr>`;
                    // Tentukan Class
                    let classRight = "empty";
                    if (right_company.is_company == 1) {
                        classRight = "company";
                    }
                    if (right_company.is_home == 1) {
                        classRight = "home";
                    }

                    if (right_company.num_occupant > 0) {
                        tableDataRight += `
                        <td class='${classRight}' id='${right_company.id}'>
                            <div class="dot">${right_company.teams[0].id}</div>
                        </td>`;
                    } else {
                        tableDataRight += `
                        <td class='${classRight}' id='${right_company.id}'></td>
                        `;
                    }
                    tableDataRight += `</tr>`;
                });
                console.log(tableDataRight);

                $("#mainTableRight").html(tableDataRight);
                // END TABLE RIGHT

                $(".btn-control-action").attr("disabled", false);
            },
        });
    });

</script>
@endsection