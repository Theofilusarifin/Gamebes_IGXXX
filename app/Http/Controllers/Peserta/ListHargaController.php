<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListHargaController extends Controller
{
    public function index(){
        return view('peserta.harga.index');
    }
}
