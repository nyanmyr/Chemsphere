<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChemicalsController extends Controller
{
    function chemicals() {
        $data = DB::table('chemicals')->get();
        $user = Auth::user();
        return view('inventory', ['data'=>$data,'user'=>$user]);
    }
}
