<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class LocationsController extends Controller
{
    function locations() {
        $data = DB::table('locations')->get();
        $user = Auth::user();
        return view('locations', ['data'=>$data,'user'=>$user]);
    }
}
