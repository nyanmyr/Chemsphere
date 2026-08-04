<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChemicalsController extends Controller
{
    function chemicals() {
        $data = DB::table('chemicals')->get();
        return view('inventory', ['data'=>$data]);
    }
}
