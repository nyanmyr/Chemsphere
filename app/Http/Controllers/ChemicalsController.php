<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\GHSSymbol;
use App\Models\AuditLog;
use App\Models\Chemical;
use App\SafetyClass;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChemicalsController extends Controller
{
    public function chemicals()
    {
        $data = DB::table('chemicals')->get();
        $user = Auth::user();

        return view('inventory', ['data' => $data, 'user' => $user]);
    }

    public function delete($chemical_id)
    {
        $chemical = Chemical::where(
            'chemical_id',
            $chemical_id
        )->firstOrFail()->delete();

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
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
            'user_id' => Auth::user()['user_id'],
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
            'use_amount' => 'required|numeric|min:0|max:' . $chemical['current_quantity']
        ]);

        $validated['current_quantity'] = $chemical['current_quantity'] - $validated['use_amount'];

        if (($key = array_search('use_amount', $validated)) !== false) {
            unset($validated[$key]);
        }

        $chemical->update($validated);

        return redirect()->route('inventory')->with('success', 'Location updated successfully');
    }
}
