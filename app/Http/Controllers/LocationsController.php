<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;

class LocationsController extends Controller
{
    public function locations() {
        $data = DB::table('locations')->get();
        $user = Auth::user();
        return view('locations', ['data'=>$data,'user'=>$user]);
    }

    public function delete($location_id)
    {
        $location = Location::where(
            'location_id',
            $location_id
        )->delete();
        return redirect()->route('locations')->with('success', 'Location deleted successfully');
    }
}
