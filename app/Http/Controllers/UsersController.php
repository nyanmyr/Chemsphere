<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function users()
    {
        $data = DB::table('users')->get();
        $user = Auth::user();
        return view('users', ['data' => $data, 'user' => $user]);
    }
}
