<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Machine;
use Illuminate\Http\Request;

class DescriptionController extends Controller
{
    public function index()
    {
        $machines = Machine::all();
        return view('peserta.deskripsi.index', compact(
            'machines',
        ));
    }
}
