<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        )->firstOrFail()->delete();
        return redirect()->route('locations')->with('success', 'Location deleted successfully');
    }

    public function edit($location_id)
    {
        $location = Location::where(
            'location_id',
            $location_id
        )->firstOrFail();
        return view('edit_location', compact('location'));
    }

    public function update(Request $request, $location_id)
    {
        $location = Location::where(
            'location_id',
            $location_id
        )->firstOrFail();
        $location->update([
            'location_name' => $request->location_name,
            'description' => $request->description
        ]);
        return redirect()->route('locations')->with('success', 'Location updated successfully');
    }
}
