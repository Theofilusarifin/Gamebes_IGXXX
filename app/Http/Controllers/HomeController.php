<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user() != null){
            if (Auth::user()->role == 'ketua' || Auth::user()->role == 'peserta'){
                return redirect()->route('peserta.index');
            }
            else if (Auth::user()->role == 'penpos' || Auth::user()->role == 'si') {
                return redirect()->route('penpos.index');
            }
        }
        else{
            return view('auth.login');
        }
    }
}
