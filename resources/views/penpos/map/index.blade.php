@extends('penpos.layouts.app')

@section('style')
<style>
    td,th {
    width: 1.5%;
    height: 35px;
    border: 1px dashed rgb(84, 84, 84);
    }

    tr{
        width: 100%;
        height: 35px;
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
    <thead>
        <tr>
            @for ($i = 1; $i <= $column; $i++) 
            <th></th>
            @endfor
        </tr>
    </thead>
    @php($index = 1)
    @foreach ($territories as $territory)
    
    {{-- Buka Tr --}}
    @if ($index == 1 || $index % $column == 1) 
        @php($max_colspan = 1)
            @php($dibuka = $index)
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
        <td class="{{ $class }}" rowspan="{{ $territory->rowspan }}" colspan="{{ $territory->colspan }}">{{ $index }}</td>
            @if ($territory->colspan > $max_colspan)
                @php($max_colspan = $territory->colspan)
            @endif
        {{-- Nutup tr --}}
        @if($index == $dibuka + $column)
            </tr>
            @for ($i = 1; $i < $max_colspan; $i++)
                <tr></tr>
            @endfor
        @endif
        @php($index += $territory->colspan)
    @endforeach
</table>
@endsection