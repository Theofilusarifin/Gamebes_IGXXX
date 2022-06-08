@extends('penpos.layouts.app')

@section('style')
<style>
    td {
    width: 5%;
    height: 35px;
    border: 1px dashed rgb(84, 84, 84);
    }

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
<table id="mainTable">
    @php($index = 0)
    @foreach ($territories as $territory)
        {{-- Buka Tr --}}
        @if ($index == 0 || $index % $column == 0) 
            @php($dibuka = $index)
            <tr>
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
        @if($index == $dibuka + $column)
            </tr>
        @endif
        @php($index += $territory->colspan)
    @endforeach
</table>
@endsection