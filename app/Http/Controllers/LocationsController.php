<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationsController extends Controller
{
    public function locations()
    {
        $data = DB::table('locations')->get();
        $user = Auth::user();
        return view('locations', ['data' => $data, 'user' => $user]);
    }

    public function delete($location_id)
    {
        $location = Location::where(
            'location_id',
            $location_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted location',
        ]);

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
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $location = Location::where(
            'location_id',
            $location_id
        )->firstOrFail();

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated location',
        ]);

        $location->update($validated);
        return redirect()->route('locations')->with('success', 'Location updated successfully');
    }
}
