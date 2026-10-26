<?php

namespace App\Http\Controllers;

use App\Enums\AlertType;
use App\Enums\AuditAction;
use App\Enums\EquipmentStatus;
use App\Enums\ItemType;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\UsageLog;
use App\Services\EquipmentAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    private const ATTENTION_TYPES = [
        AlertType::MAINTENANCE_CLOSE_TO_DUE,
        AlertType::MAINTENANCE_OVERDUE,
    ];

    public function equipment(Request $request, EquipmentAlertService $alerts)
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
            'search_date_received_min' => 'nullable|date',
            'search_date_received_max' => 'nullable|date',
            'search_preventive_maintenance_date_min' => 'nullable|date',
            'search_preventive_maintenance_date_max' => 'nullable|date',
            'search_next_due_min' => 'nullable|date',
            'search_next_due_max' => 'nullable|date',
            'search_alert_type' => 'nullable|array',
            'search_alert_type.*' => ['required', Rule::enum(AlertType::class)->only(self::ATTENTION_TYPES)],
            'sort' => 'nullable|in:recent,oldest',
        ]);

        $query = Equipment::query();

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

        $query->when($request->filled('search_date_received_min'), function ($q) use ($request) {
            $q->whereDate('date_received', '>=', $request['search_date_received_min']);
        });

        $query->when($request->filled('search_date_received_max'), function ($q) use ($request) {
            $q->whereDate('date_received', '<=', $request['search_date_received_max']);
        });

        $query->when($request->filled('search_preventive_maintenance_date_min'), function ($q) use ($request) {
            $q->whereDate('preventive_maintenance_date', '>=', $request['search_preventive_maintenance_date_min']);
        });

        $query->when($request->filled('search_preventive_maintenance_date_max'), function ($q) use ($request) {
            $q->whereDate('preventive_maintenance_date', '<=', $request['search_preventive_maintenance_date_max']);
        });

        $query->when($request->filled('search_next_due_min'), function ($q) use ($request) {
            $q->whereDate('next_due', '>=', $request['search_next_due_min']);
        });

        $query->when($request->filled('search_next_due_max'), function ($q) use ($request) {
            $q->whereDate('next_due', '<=', $request['search_next_due_max']);
        });

        // Ticking both alert types shows equipment in either state.
        $query->when($request->filled('search_alert_type'), function ($q) use ($request, $alerts) {
            $types = collect($request['search_alert_type'])->map(fn ($value) => AlertType::from($value));

            $q->where(function ($sub) use ($types, $alerts) {
                foreach ($types as $type) {
                    $sub->orWhere(fn ($w) => $this->applyCondition($w, $type, $alerts->closeToDueMonths()));
                }
            });
        });

        $direction = $request->input('sort', 'recent') === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('created_at', $direction)->orderBy('equipment_id', $direction);

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('info', __('messages.equipment.none_found'));
        }

        $statuses = $data->getCollection()
            ->mapWithKeys(fn (Equipment $e) => [$e->equipment_id => $alerts->maintenanceStatus($e)]);
        $alertTypes = self::ATTENTION_TYPES;

        return view('equipment', compact('data', 'user', 'statuses', 'alertTypes'));
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
            'date_received' => 'required|date',
            'preventive_maintenance_date' => 'required|date',
            'next_due' => 'required|date'
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

    public function use_edit($equipment_id, EquipmentAlertService $alerts)
    {
        $equipment = Equipment::where(
            'equipment_id',
            $equipment_id
        )->firstOrFail();

        switch ($equipment['status']) {
            case EquipmentStatus::OUT_OF_ORDER->value:
                return back()->with('error', __('messages.equipment.out_of_order'));
            case EquipmentStatus::FOR_DISPOSAL->value:
                return back()->with('error', __('messages.equipment.for_disposal'));
            case EquipmentStatus::IN_USE->value:
                return back()->with('error', __('messages.equipment.in_use'));
        }

        $maintenance = $alerts->maintenanceStatus($equipment);

        return view('use_equipment', compact('equipment', 'maintenance'));
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

        $equipment['status'] = EquipmentStatus::IN_USE;

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
            'quantity_remaining' => 0,
            'notes' => $notes
        ]);

        return redirect()->route('equipment')->with('success', __('messages.equipment.used'));
    }

    private function applyCondition($query, AlertType $type, int $closeToDueMonths): void
    {
        match ($type) {
            AlertType::MAINTENANCE_OVERDUE => $query->whereDate('next_due', '<=', today()),
            AlertType::MAINTENANCE_CLOSE_TO_DUE => $query
                ->whereDate('next_due', '>', today())
                ->whereDate('next_due', '<=', today()->addMonthsNoOverflow($closeToDueMonths)),
        };
    }
}
