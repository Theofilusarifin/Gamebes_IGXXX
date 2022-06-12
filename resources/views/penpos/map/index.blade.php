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
        background-color: #000000
    }
    .harbour{
        width: 30px;
        background-color: #EA4335;
        cursor: pointer;
    }
    .company{
        background-color: #FFFFFF;
        /* border: 1px dashed rgb(84, 84, 84); */
        height: 0px;
        width: 0px;
    }

    .machine_store{
        background-color: green;
        color: #FFF;
    }

    .ingridient_store{
        background-color: yellow;
    }

    .transport_store{
        background-color: red;  
        color: #FFF;
    }

    .service{
        background-color: grey;
        color: #FFF;
    }

    td:not(.water,.company){
        border: 1px dashed rgb(84, 84, 84);
    }
</style>
@endsection

@section('content')
<div class="row my-2 d-flex mb-3">
    <div class="col-10">
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
                @if ($territory->is_wall) @php($class="wall")
                @elseif ($territory->is_water) @php($class="water")
                @elseif ($territory->is_harbour) @php($class="harbour")
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

                <td class="{{ $class }}" id="{{ $territory->id }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}">{{ $alias }}</td>
                {{-- Buat Td --}}
                
                {{-- Nutup tr --}}
                @if($territory->close_tr)
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
    <div class="col-2">
    </div>
</div>
@endsection