<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Enums\EquipmentStatus;
use App\Enums\ItemType;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'search_purchase_date_min' => 'nullable|date',
            'search_purchase_date_max' => 'nullable|date',
            'search_warranty_expiration_min' => 'nullable|date',
            'search_warranty_expiration_max' => 'nullable|date',
            'search_last_maintenance_min' => 'nullable|date',
            'search_last_maintenance_max' => 'nullable|date',
            'search_next_maintenance_min' => 'nullable|date',
            'search_next_maintenance_max' => 'nullable|date',
        ]);

        $query = Equipment::with('location');

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('equipment_name', 'LIKE', "%{$request['search']}%")
                    ->orWhere('model', 'LIKE', "%{$request['search']}%")
                    ->orWhere('serial_id', 'LIKE', "%{$request['search']}%");
            });
        });

        $query->when($request->filled('search_equipment_id_min'), function ($q) use ($request) {
            $q->where('equipment_id', '>=', $request['search_equipment_id_min']);
        });

        $query->when($request->filled('search_equipment_id_max'), function ($q) use ($request) {
            $q->where('equipment_id', '<=', $request['search_equipment_id_max']);
        });

        $query->when($request->filled('search_location_id_min'), function ($q) use ($request) {
            $q->where('location_id', '>=', $request['search_location_id_min']);
        });

        $query->when($request->filled('search_location_id_max'), function ($q) use ($request) {
            $q->where('location_id', '<=', $request['search_location_id_max']);
        });

        $query->when($request->filled('search_created_by_min'), function ($q) use ($request) {
            $q->where('created_by', '>=', $request['search_created_by_min']);
        });

        $query->when($request->filled('search_created_by_max'), function ($q) use ($request) {
            $q->where('created_by', '<=', $request['search_created_by_max']);
        });

        $query->when($request->filled('search_status'), function ($q) use ($request) {
            $q->whereIn('status', (array) $request['search_status']);
        });

        $query->when($request->filled('search_purchase_date_min'), function ($q) use ($request) {
            $q->whereDate('purchase_date', '>=', $request['search_purchase_date_min']);
        });

        $query->when($request->filled('search_purchase_date_max'), function ($q) use ($request) {
            $q->whereDate('purchase_date', '<=', $request['search_purchase_date_max']);
        });

        $query->when($request->filled('search_warranty_expiration_min'), function ($q) use ($request) {
            $q->whereDate('warranty_expiration', '>=', $request['search_warranty_expiration_min']);
        });

        $query->when($request->filled('search_warranty_expiration_max'), function ($q) use ($request) {
            $q->whereDate('warranty_expiration', '<=', $request['search_warranty_expiration_max']);
        });

        $query->when($request->filled('search_last_maintenance_min'), function ($q) use ($request) {
            $q->whereDate('last_maintenance', '>=', $request['search_last_maintenance_min']);
        });

        $query->when($request->filled('search_last_maintenance_max'), function ($q) use ($request) {
            $q->whereDate('last_maintenance', '<=', $request['search_last_maintenance_max']);
        });

        $query->when($request->filled('search_next_maintenance_min'), function ($q) use ($request) {
            $q->whereDate('next_maintenance', '>=', $request['search_next_maintenance_min']);
        });

        $query->when($request->filled('search_next_maintenance_max'), function ($q) use ($request) {
            $q->whereDate('next_maintenance', '<=', $request['search_next_maintenance_max']);
        });

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('info', __('messages.equipment.none_found'));
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

        return redirect()->route('equipment')->with('success', __('messages.equipment.deleted'));
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

        return redirect()->route('equipment')->with('success', __('messages.equipment.updated'));
    }

    public function use_edit($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        switch ($equipment['status']) {
            case EquipmentStatus::UNAVAILABLE->value:
                return back()->with('error', __('messages.equipment.unavailable'));
            case EquipmentStatus::BROKEN->value:
                return back()->with('error', __('messages.equipment.broken'));
            case EquipmentStatus::UNDER_MAINTENANCE->value:
                return back()->with('error', __('messages.equipment.under_maintenance'));
        }

        return view('use_equipment', compact('equipment'));
    }

    public function use_update(Request $request, $equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        $validated = $request->validate(['notes' => 'nullable|string']);

        $notes = $validated['notes'];

        if (($key = array_search('notes', $validated)) !== false) {
            unset($validated[$key]);
        }

        $equipment['status'] = EquipmentStatus::UNAVAILABLE;

        $equipment->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'used equipment',
        ]);

        UsageLog::create([
            'created_by' => Auth::user()['user_id'],
            'location_id' => $equipment['location_id'],
            'item_type' => ItemType::EQUIPMENT,
            'item_id' => $equipment['equipment_id'],
            'quantity_used' => 0,
            'quantity_remaining' => 0,
            'notes' => $notes
        ]);

        return redirect()->route('equipment')->with('success', __('messages.equipment.used'));
    }
}
