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
    public function equipment()
    {
        $data = DB::table('equipment')->get();
        $user = Auth::user();

        return view('equipment', ['data' => $data, 'user' => $user]);
    }

    public function delete($equipment_id)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
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
            'quantity' => 'required|numeric|min:0|max:9999997.999',
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
            'user_id' => Auth::user()['user_id'],
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

        // $validated = $request->validate([
        //     'use_amount' => 'required|numeric|min:0|max:' . $chemical['current_quantity']
        // ]);

        // $validated['current_quantity'] = $chemical['current_quantity'] - $validated['use_amount'];

        // if (($key = array_search('use_amount', $validated)) !== false) {
        //     unset($validated[$key]);
        // }

        // $chemical->update($validated);

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated equipment',
        ]);

        UsageLog::create([
            'user_id' => Auth::user()['user_id'],
            'location_id' => $equipment['location_id'],
            'item_type' => ItemType::EQUIPMENT,
            'item_id' => $equipment['equipment_id'],
            'quantity_used' => 0.000,
            'quantity_remaining' => $equipment['quantity']
        ]);

        return redirect()->route('equipment')->with('success', 'Equipment updated successfully');
    }
}
