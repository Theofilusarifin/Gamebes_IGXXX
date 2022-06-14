<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        //Declare
        $team = Auth::user()->team;
        $team_products = $team->products->where('pivot.amount_have', '>', '0')->all();
        $team_ingridients = $team->ingridients->where('pivot.amount_have', '>', '0')->all();

        return view('peserta.inventory.index', compact(
            'team',
            'team_products',
            'team_ingridients'
        ));
    }
}
