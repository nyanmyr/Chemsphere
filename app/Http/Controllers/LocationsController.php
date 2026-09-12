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
    public function locations(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_location_id_min' => 'nullable|integer|min:1',
            'search_location_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
        ]);

        $query = Location::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('location_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        });

        $query->when($request->filled('search_location_id_min'), function ($q) use ($request) {
            $q->where('location_id', '>=', $request->search_location_id_min);
        });

        $query->when($request->filled('search_location_id_max'), function ($q) use ($request) {
            $q->where('location_id', '<=', $request->search_location_id_max);
        });

        $query->when($request->filled('search_created_by_min'), function ($q) use ($request) {
            $q->where('created_by', '>=', $request->search_created_by_min);
        });

        $query->when($request->filled('search_created_by_max'), function ($q) use ($request) {
            $q->where('created_by', '<=', $request->search_created_by_max);
        });

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->now('error', 'No location records found matching your range criteria.');
        }

        return view('locations', compact('data', 'user'));
    }

    public function delete($location_id)
    {
        $location = Location::where(
            'location_id',
            $location_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
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
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated location',
        ]);

        $location->update($validated);
        return redirect()->route('locations')->with('success', 'Location updated successfully');
    }
}
