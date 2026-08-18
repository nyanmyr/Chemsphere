<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{
    function locations() {
        $data = DB::table('locations')->get();
        return view('locations', ['data'=>$data]);
    }
}
