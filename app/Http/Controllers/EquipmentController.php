<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function equipment()
    {
        $data = DB::table('equipment')->get();
        $user = Auth::user();

        return view('equipment', ['data' => $data, 'user' => $user]);
    }
}
