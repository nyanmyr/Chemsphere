<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\ItemType;
use App\GHSSymbol;
use App\Models\AuditLog;
use App\Models\Chemical;
use App\SafetyClass;
use App\Unit;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChemicalsController extends Controller
{
    public function chemicals(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'chemical_id_min' => 'nullable|integer',
            'chemical_id_max' => 'nullable|integer',
            'location_id_min' => 'nullable|integer',
            'location_id_max' => 'nullable|integer',
            'created_by_min' => 'nullable|integer',
            'created_by_max' => 'nullable|integer',
            'search_volume_per_unit_min' => 'nullable|numeric|min:0|max:9999997.999',
            'search_volume_per_unit_max' => 'nullable|numeric|min:0|max:9999997.999',
            'search_initial_quantity_min' => 'nullable|numeric|min:0|max:9999997.999',
            'search_initial_quantity_max' => 'nullable|numeric|min:0|max:9999997.999',
            'search_current_quantity_min' => 'nullable|numeric|min:0|max:9999997.999',
            'search_current_quantity_max' => 'nullable|numeric|min:0|max:9999997.999',
            'search_safety_classes' => 'nullable|array',
            'search_ghs_symbols' => 'nullable|array',
            'search_unit' => 'nullable|array',
        ]);

        $query = chemical::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('chemical_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('batch_number', 'LIKE', "%{$request->search}%")
                    ->orWhere('brand_name', 'LIKE', "%{$request->search}%");
            });
        });

        $query->when($request->filled('chemical_id_min'), function ($q) use ($request) {
            $q->where('chemical_id', '>=', $request->chemical_id_min);
        });

        $query->when($request->filled('chemical_id_max'), function ($q) use ($request) {
            $q->where('chemical_id', '<=', $request->chemical_id_max);
        });

        $query->when($request->filled('location_id_min'), function ($q) use ($request) {
            $q->where('location_id', '>=', $request->location_id_min);
        });

        $query->when($request->filled('location_id_max'), function ($q) use ($request) {
            $q->where('location_id', '<=', $request->location_id_max);
        });

        $query->when($request->filled('created_by_min'), function ($q) use ($request) {
            $q->where('created_by', '>=', $request->created_by_min);
        });

        $query->when($request->filled('created_by_max'), function ($q) use ($request) {
            $q->where('created_by', '<=', $request->created_by_max);
        });

        $query->when($request->filled('search_volume_per_unit_min'), function ($q) use ($request) {
            $q->where('volume_per_unit', '>=', $request->search_volume_per_unit_min);
        });

        $query->when($request->filled('search_volume_per_unit_max'), function ($q) use ($request) {
            $q->where('volume_per_unit', '<=', $request->search_volume_per_unit_max);
        });

        $query->when($request->filled('search_initial_quantity_min'), function ($q) use ($request) {
            $q->where('initial_quantity', '>=', $request->search_initial_quantity_min);
        });

        $query->when($request->filled('search_initial_quantity_max'), function ($q) use ($request) {
            $q->where('initial_quantity', '<=', $request->search_initial_quantity_max);
        });

        $query->when($request->filled('search_current_quantity_min'), function ($q) use ($request) {
            $q->where('current_quantity', '>=', $request->search_current_quantity_min);
        });

        $query->when($request->filled('search_current_quantity_max'), function ($q) use ($request) {
            $q->where('current_quantity', '<=', $request->search_current_quantity_max);
        });

        $query->when($request->filled('search_user_role'), function ($q) use ($request) {
            $q->whereIn('user_role', (array) $request->search_user_role);
        });

        $query->when($request->filled('search_safety_classes'), function ($q) use ($request) {
            $classes = (array) $request->search_safety_classes;

            $q->where(function ($sub) use ($classes) {
                foreach ($classes as $class) {
                    $sub->orWhereRaw('FIND_IN_SET(?, safety_classes)', [$class]);
                }
            });
        });

        $query->when($request->filled('search_ghs_symbols'), function ($q) use ($request) {
            $classes = (array) $request->search_ghs_symbols;

            $q->where(function ($sub) use ($classes) {
                foreach ($classes as $class) {
                    $sub->orWhereRaw('FIND_IN_SET(?, ghs_symbols)', [$class]);
                }
            });
        });

        $query->when($request->filled('search_unit'), function ($q) use ($request) {
            $q->whereIn('unit', (array) $request->search_unit);
        });

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->now('error', 'No chemical records found matching your range criteria.');
        }

        return view('inventory', compact('data', 'user'));
    }

    public function delete($chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted chemical',
        ]);

        return redirect()->route('inventory')->with('success', 'Location deleted successfully');
    }

    public function edit($chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail();

        return view('edit_chemical', compact('chemical'));
    }

    public function update(Request $request, $chemical_id)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,location_id',
            'chemical_name' => 'required|string|max:255',
            'batch_number' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'volume_per_unit' => 'required|numeric|min:0|max:9999997.999',
            'initial_quantity' => 'required|numeric|min:0|max:9999997.999',
            'current_quantity' => 'required|numeric|min:0|max:9999997.999',
            'expiration_date' => 'required|date',
            'arrival_date' => 'required|date',
            'safety_classes' => 'nullable|array',
            'safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
            'ghs_symbols' => 'nullable|array',
            'ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
            'unit' => ['required', Rule::enum(Unit::class)],
        ]);

        if (!empty($validated['safety_classes'])) {
            $validated['safety_classes'] = implode(',', array_map(
                fn($item) => $item instanceof \BackedEnum ? $item->value : $item,
                $validated['safety_classes']
            ));
        } else {
            $validated['safety_classes'] = "";
        }

        if (!empty($validated['ghs_symbols'])) {
            $validated['ghs_symbols'] = implode(',', array_map(
                fn($item) => $item instanceof \BackedEnum ? $item->value : $item,
                $validated['ghs_symbols']
            ));
        } else {
            $validated['ghs_symbols'] = "";
        }

        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail();

        $chemical->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated chemical',
        ]);

        return redirect()->route('inventory')->with('success', 'Location updated successfully');
    }

    public function use_edit($chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail();

        if ($chemical['current_quantity'] <= 0) {
            return back()->withErrors(['current_quantity' => 'Current quantity is 0']);
        }

        return view('use_chemical', compact('chemical'));
    }

    public function use_update(Request $request, $chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail();

        $validated = $request->validate([
            'use_amount' => 'required|numeric|min:0|max:' . $chemical['current_quantity'],
            'notes' => 'nullable|string',
        ]);

        $validated['current_quantity'] = $chemical['current_quantity'] - $validated['use_amount'];

        $notes = $validated['notes'];

        if (($key = array_search('use_amount', $validated)) !== false) {
            unset($validated[$key]);
        }

        if (($key = array_search('notes', $validated)) !== false) {
            unset($validated[$key]);
        }

        $chemical->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated chemical',
        ]);

        UsageLog::create([
            'created_by' => Auth::user()['user_id'],
            'location_id' => $chemical['location_id'],
            'item_type' => ItemType::CHEMICAL,
            'item_id' => $chemical['chemical_id'],
            'quantity_used' => $validated['use_amount'],
            'quantity_remaining' => $validated['current_quantity'],
            'notes' => $notes
        ]);

        return redirect()->route('inventory')->with('success', 'Location updated successfully');
    }
}
