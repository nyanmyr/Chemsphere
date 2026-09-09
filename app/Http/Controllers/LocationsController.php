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
        $search = $request->input('search');

        $data = DB::table('locations')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('location_name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
            });
        })
        ->get();

        $user = Auth::user();

        return view('locations', compact('data', 'user'));
    }

    public function locationsIDSearch(Request $request, string $type)
    {
        $validSearchTypes = [
            'created_by' => 'created_by'
        ];

        // 2. Catch unsupported search types
        if (!array_key_exists($type, $validSearchTypes)) {
            return redirect()
                ->route('locations')
                ->with('error', "Search type '{$type}' is not currently supported.");
        }

        $column = $validSearchTypes[$type];
        $min = $request->input('min');
        $max = $request->input('max');

        $query = Location::query();

        $query->when(filled($min), function ($q) use ($column, $min) {
            return $q->where($column, '>=', (int) $min);
        });

        $query->when(filled($max), function ($q) use ($column, $max) {
            return $q->where($column, '<=', (int) $max);
        });

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->now('error', 'No location records found matching your range criteria.');
        }

        $user = Auth::user();

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
