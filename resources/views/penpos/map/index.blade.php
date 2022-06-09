@extends('penpos.layouts.app')

@section('style')
<style>
    td,th {
    width: 1.5%;
    height: 35px;
    border: 1px dashed rgb(84, 84, 84);
    }
/* 
    table {border-collapse:collapse; table-layout:fixed; width:310px;}
    table td {border:solid 1px #fab; width:100px; word-wrap:break-word;} */

    /* tr{
        width: 100%;
        height: 35px;
    } */

    .water{
        background-color: #8DB5F8
    }
    .wall{
        background-color: #000000
    }
    .harbour{
        background-color: #EA4335
    }
    .company{
        background-color: #FFFFFF;
    }
</style>
@endsection

@section('content')
@php($column = 60)
<table id="mainTable" class="m-4">
    <thead>
        <tr>
            @for ($i = 1; $i <= $column; $i++) 
            <th></th>
            @endfor
        </tr>
    </thead>
    @foreach ($territories as $territory)
        {{-- Buka Tr --}}
        @if($territory->open_tr)
            <tr >
        @endif

        {{-- Inisialisasi Class --}}
        @php($class="")
        @if ($territory->is_wall) @php($class="wall")
        @elseif ($territory->is_water) @php($class="water")
        @elseif ($territory->is_harbour) @php($class="harbour")
        @elseif ($territory->is_company) @php($class="company")
        @endif

        {{-- Buat Td --}}
        <td class="{{ $class }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}"></td>
        
        {{-- Nutup tr --}}
        @if($territory->close_tr)
            </tr>
        @endif
        {{-- @php($index += $territory->colspan) --}}
    @endforeach
</table>
@endsection