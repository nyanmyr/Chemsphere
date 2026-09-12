<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\EquipmentStatus;
use App\ItemType;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function equipment(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_equipment_id_min' => 'nullable|integer|min:1',
            'search_equipment_id_max' => 'nullable|integer|min:1',
            'search_location_id_min' => 'nullable|integer|min:1',
            'search_location_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
            'search_status' => 'nullable|array',
        ]);

        $query = Equipment::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('equipment_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('model', 'LIKE', "%{$request->search}%")
                    ->orWhere('serial_id', 'LIKE', "%{$request->search}%");
            });
        });

        $query->when($request->filled('search_equipment_id_min'), function ($q) use ($request) {
            $q->where('equipment_id', '>=', $request->search_equipment_id_min);
        });

        $query->when($request->filled('search_equipment_id_max'), function ($q) use ($request) {
            $q->where('equipment_id', '<=', $request->search_equipment_id_max);
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

        $query->when($request->filled('search_status'), function ($q) use ($request) {
            $q->whereIn('status', (array) $request->search_status);
        });

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->now('error', 'No equipment records found matching your range criteria.');
        }

        return view('equipment', compact('data', 'user'));
    }

    public function delete($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted equipment',
        ]);

        return redirect()->route('equipment')->with('success', 'Equipment deleted successfully');
    }

    public function edit($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        return view('edit_equipment', compact('equipment'));
    }

    public function update(Request $request, $equipment_id)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,location_id',
            'equipment_name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_id' => 'required|string|max:255',
            'status' => ['required', Rule::enum(EquipmentStatus::class)],
            'initial_quantity' => 'required|numeric|min:0|max:9999999999.999',
            'current_quantity' => 'required|numeric|min:0|max:9999999999.999',
            'purchase_date' => 'required|date',
            'warranty_expiration' => 'required|date',
            'last_maintenance' => 'required|date',
            'next_maintenance' => 'required|date'
        ]);

        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        $equipment->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated equipment',
        ]);

        return redirect()->route('equipment')->with('success', 'Equipment updated successfully');
    }

    public function use_edit($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        if ($equipment['quantity'] <= 0) {
            return back()->withErrors(['quantity' => 'Quantity is 0']);
        }

        switch($equipment['status']) {
            case EquipmentStatus::UNAVAILABLE:
                return back()->withErrors(['quantity' => 'Equipment currently unavailable']);
            case EquipmentStatus::BROKEN:
                return back()->withErrors(['quantity' => 'Equipment currently broken']);
            case EquipmentStatus::UNDER_MAINTENANCE:
                return back()->withErrors(['quantity' => 'Equipment currently under maintenance']);
        }

        return view('use_equipment', compact('equipment'));
    }

    public function use_update(Request $request, $equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        $validated = $request->validate([
            'use_amount' => 'required|numeric|min:0|max:' . $equipment['current_quantity'],
            'notes' => 'nullable|string',
        ]);

        $validated['current_quantity'] = $equipment['current_quantity'] - $validated['use_amount'];

        $notes = $validated['notes'];

        if (($key = array_search('use_amount', $validated)) !== false) {
            unset($validated[$key]);
        }

        if (($key = array_search('notes', $validated)) !== false) {
            unset($validated[$key]);
        }

        // $chemical->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated equipment',
        ]);

        UsageLog::create([
            'created_by' => Auth::user()['user_id'],
            'location_id' => $equipment['location_id'],
            'item_type' => ItemType::EQUIPMENT,
            'item_id' => $equipment['equipment_id'],
            'quantity_used' => 0.000,
            'quantity_remaining' => $equipment['quantity'],
            'notes' => $notes
        ]);

        return redirect()->route('equipment')->with('success', 'Equipment updated successfully');
    }
}
