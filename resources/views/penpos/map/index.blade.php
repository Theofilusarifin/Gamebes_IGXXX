@extends('penpos.layouts.app')

@section('style')
<style>
    td {
    min-width: 10px;
    min-height: 10px;
    width: 30px;
    height: 30px;
    /* border: 1px; */
    }
    /* tr{
        width: 100%;
        height: 35px;
    } */

    .water{
        width: 10px;
        height: 15px;
        background-color: #8DB5F8
    }
    .wall{
        background-color: #000000
    }
    .harbour{
        width: 40px;
        background-color: #EA4335;
        cursor: pointer;
    }
    .company{
        background-color: #FFFFFF;
        /* border: 1px dashed rgb(84, 84, 84); */
    }

    .machine_store{
        background-color: green;
    }

    .ingridient_store{
        background-color: yellow;
    }

    .transport_store{
        background-color: red
    }

    .service{
        background-color: grey;
    }

    td:not(.water,.company){
        border: 1px dashed rgb(84, 84, 84);
    }
</style>
@endsection

@section('content')
@php($column = 60)
<table id="mainTable" class="m-4">
    @foreach ($territories as $territory)
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
        
        {{-- Transport Store --}}
        @elseif (isset($territory->transport_store_id)) @php($class="transport_store")
        @elseif (isset($territory->ingridient_store_id)) @php($class="ingridient_store")
        @elseif (isset($territory->machine_store_id)) @php($class="machine_store")
        @elseif (isset($territory->service_id)) @php($class="service")
        @endif

        <td class="{{ $class }}" id="{{ $territory->id }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}"></td>
        {{-- Buat Td --}}
        
        {{-- Nutup tr --}}
        @if($territory->close_tr)
            </tr>
        @endif
    @endforeach
</table>
@endsection