<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\EquipmentStatus;
use App\Models\AuditLog;
use App\Models\Equipment;
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

        return redirect()->route('equipment')->with('success', 'Location updated successfully');
    }
}
