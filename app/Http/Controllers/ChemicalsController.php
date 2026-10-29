<?php

namespace App\Http\Controllers;

use App\Enums\AlertType;
use App\Enums\AuditAction;
use App\Enums\GHSSymbol;
use App\Enums\ItemType;
use App\Enums\SafetyClass;
use App\Enums\Unit;
use App\Models\AuditLog;
use App\Models\Chemical;
use App\Models\ChemicalBatch;
use App\Models\Location;
use App\Models\UsageLog;
use App\Services\ChemicalAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChemicalsController extends Controller
{
    private const EXPIRATION_TYPES = [
        AlertType::EXPIRING_SOON,
        AlertType::EXPIRED,
    ];

    public function chemicals(Request $request, ChemicalAlertService $alerts)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'search_chemical_id_min' => 'nullable|integer|min:1',
            'search_chemical_id_max' => 'nullable|integer|min:1',
            'search_location_id_min' => 'nullable|integer|min:1',
            'search_location_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
            'search_volume_per_unit_min' => 'nullable|numeric|min:0|max:99999999.99',
            'search_volume_per_unit_max' => 'nullable|numeric|min:0|max:99999999.99',
            'search_quantity_received_min' => 'nullable|integer|min:0|max:99999999',
            'search_quantity_received_max' => 'nullable|integer|min:0|max:99999999',
            'search_current_quantity_min' => 'nullable|integer|min:0|max:99999999',
            'search_current_quantity_max' => 'nullable|integer|min:0|max:99999999',
            'search_expiration_date_min' => 'nullable|date',
            'search_expiration_date_max' => 'nullable|date',
            'search_date_received_min' => 'nullable|date',
            'search_date_received_max' => 'nullable|date',
            'search_safety_classes' => 'nullable|array',
            'search_safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
            'search_ghs_symbols' => 'nullable|array',
            'search_ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
            'search_unit' => 'nullable|array',
            'search_unit.*' => ['required', Rule::enum(Unit::class)],
            'search_alert_type' => 'nullable|array',
            'search_alert_type.*' => ['required', Rule::enum(AlertType::class)->only(self::EXPIRATION_TYPES)],
            'sort' => 'nullable|in:recent,oldest',
        ]);

        $query = Chemical::query()
            ->withCount('batches')
            ->withSum('batches as quantity_received_total', 'quantity_received')
            ->withSum('batches as current_quantity_total', 'current_quantity')
            ->withMin('batches as next_expiration_date', 'expiration_date');

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('chemical_name', 'LIKE', "%{$request['search']}%")
                    ->orWhereHas('batches', function ($batchQuery) use ($request) {
                        $batchQuery->where('batch_number', 'LIKE', "%{$request['search']}%")
                            ->orWhere('brand_name', 'LIKE', "%{$request['search']}%");
                    });
            });
        });

        foreach (['min' => '>=', 'max' => '<='] as $bound => $operator) {
            $query->when($request->filled("search_chemical_id_{$bound}"), function ($q) use ($request, $bound, $operator) {
                $q->where('chemical_id', $operator, $request["search_chemical_id_{$bound}"]);
            });

            $query->when($request->filled("search_created_by_{$bound}"), function ($q) use ($request, $bound, $operator) {
                $q->where('created_by', $operator, $request["search_created_by_{$bound}"]);
            });
        }

        $batchFilters = [
            'search_location_id_min', 'search_location_id_max',
            'search_volume_per_unit_min', 'search_volume_per_unit_max',
            'search_quantity_received_min', 'search_quantity_received_max',
            'search_current_quantity_min', 'search_current_quantity_max',
            'search_expiration_date_min', 'search_expiration_date_max',
            'search_date_received_min', 'search_date_received_max',
            'search_unit', 'search_alert_type',
        ];

        if (collect($batchFilters)->contains(fn ($field) => $request->filled($field))) {
            $query->whereHas('batches', function ($batchQuery) use ($request, $alerts) {
                $ranges = [
                    'location_id' => 'search_location_id',
                    'quantity_per_unit' => 'search_volume_per_unit',
                    'quantity_received' => 'search_quantity_received',
                    'current_quantity' => 'search_current_quantity',
                    'expiration_date' => 'search_expiration_date',
                    'date_received' => 'search_date_received',
                ];

                foreach ($ranges as $column => $prefix) {
                    foreach (['min' => '>=', 'max' => '<='] as $bound => $operator) {
                        $field = "{$prefix}_{$bound}";

                        if (!$request->filled($field)) {
                            continue;
                        }

                        if (in_array($column, ['expiration_date', 'date_received'], true)) {
                            $batchQuery->whereDate($column, $operator, $request[$field]);
                        } else {
                            $batchQuery->where($column, $operator, $request[$field]);
                        }
                    }
                }

                if ($request->filled('search_unit')) {
                    $batchQuery->whereIn('unit', (array) $request['search_unit']);
                }

                if ($request->filled('search_alert_type')) {
                    $types = collect($request['search_alert_type'])->map(fn ($value) => AlertType::from($value));
                    $batchQuery->where(function ($sub) use ($types, $alerts) {
                        foreach ($types as $type) {
                            $sub->orWhere(fn ($condition) => $this->applyCondition($condition, $type, $alerts->expiringSoonMonths()));
                        }
                    });
                }
            });
        }

        $query->when($request->filled('search_safety_classes'), function ($q) use ($request) {
            $classes = (array) $request['search_safety_classes'];
            $q->where(function ($sub) use ($classes) {
                foreach ($classes as $class) {
                    $sub->orWhereRaw('FIND_IN_SET(?, safety_classes)', [$class]);
                }
            });
        });

        $query->when($request->filled('search_ghs_symbols'), function ($q) use ($request) {
            $symbols = (array) $request['search_ghs_symbols'];
            $q->where(function ($sub) use ($symbols) {
                foreach ($symbols as $symbol) {
                    $sub->orWhereRaw('FIND_IN_SET(?, ghs_symbols)', [$symbol]);
                }
            });
        });

        $direction = $request->input('sort', 'recent') === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('created_at', $direction)->orderBy('chemical_id', $direction);

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('info', __('messages.chemical.none_found'));
        }

        $alertTypes = self::EXPIRATION_TYPES;

        return view('inventory', compact('data', 'alertTypes'));
    }

    private function applyCondition($query, AlertType $type, int $expiringSoonMonths): void
    {
        match ($type) {
            AlertType::EXPIRED => $query->whereDate('expiration_date', '<=', today()),
            AlertType::EXPIRING_SOON => $query
                ->whereDate('expiration_date', '>', today())
                ->whereDate('expiration_date', '<=', today()->addMonthsNoOverflow($expiringSoonMonths)),
            default => null,
        };
    }

    public function create()
    {
        $chemical = null;
        return view('create_chemical', compact('chemical'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'chemical_name' => 'required|string|max:255|unique:chemicals,chemical_name',
            'safety_classes' => 'nullable|array',
            'safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
            'ghs_symbols' => 'nullable|array',
            'ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
        ]);

        $validated['safety_classes'] = implode(',', $validated['safety_classes'] ?? []);
        $validated['ghs_symbols'] = implode(',', $validated['ghs_symbols'] ?? []);
        $validated['created_by'] = Auth::user()['user_id'];

        $chemical = Chemical::create($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::CREATE,
            'target' => 'create chemical category',
        ]);

        return redirect()->route('inventory.show', $chemical->chemical_id)
            ->with('success', __('messages.chemical.created'));
    }

    public function show($chemical_id, ChemicalAlertService $alerts)
    {
        $chemical = Chemical::with(['batches.location'])->where('chemical_id', $chemical_id)->firstOrFail();
        $statuses = $chemical->batches->mapWithKeys(
            fn (ChemicalBatch $batch) => [$batch->chemical_batch_id => $alerts->expirationStatus($batch)]
        );

        return view('chemical_batches', compact('chemical', 'statuses'));
    }

    public function delete($chemical_id)
    {
        $chemical = Chemical::where('chemical_id', $chemical_id)->firstOrFail();
        $chemical->delete();

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted chemical category',
        ]);

        return redirect()->route('inventory')->with('success', __('messages.chemical.deleted'));
    }

    public function edit($chemical_id)
    {
        $chemical = Chemical::where('chemical_id', $chemical_id)->firstOrFail();
        return view('edit_chemical', compact('chemical'));
    }

    public function update(Request $request, $chemical_id)
    {
        $chemical = Chemical::where('chemical_id', $chemical_id)->firstOrFail();
        $validated = $request->validate([
            'chemical_name' => ['required', 'string', 'max:255', Rule::unique('chemicals', 'chemical_name')->ignore($chemical->chemical_id, 'chemical_id')],
            'safety_classes' => 'nullable|array',
            'safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
            'ghs_symbols' => 'nullable|array',
            'ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
        ]);

        $validated['safety_classes'] = implode(',', $validated['safety_classes'] ?? []);
        $validated['ghs_symbols'] = implode(',', $validated['ghs_symbols'] ?? []);

        $chemical->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated chemical category',
        ]);

        return redirect()->route('inventory.show', $chemical->chemical_id)->with('success', __('messages.chemical.updated'));
    }

    public function batch_create($chemical_id)
    {
        $chemical = Chemical::where('chemical_id', $chemical_id)->firstOrFail();
        $batch = null;
        $locations = Location::orderBy('location_name')->get();

        return view('create_chemical_batch', compact('chemical', 'batch', 'locations'));
    }

    public function batch_store(Request $request, $chemical_id)
    {
        $chemical = Chemical::where('chemical_id', $chemical_id)->firstOrFail();
        $validated = $request->validate($this->batchRules($chemical));
        $validated['chemical_id'] = $chemical->chemical_id;
        $validated['created_by'] = Auth::user()['user_id'];

        ChemicalBatch::create($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::CREATE,
            'target' => 'create chemical batch',
        ]);

        return redirect()->route('inventory.show', $chemical->chemical_id)->with('success', __('messages.chemical.batch_created'));
    }

    public function batch_edit($chemical_batch_id)
    {
        $batch = ChemicalBatch::with('chemical')->where('chemical_batch_id', $chemical_batch_id)->firstOrFail();
        $chemical = $batch->chemical;
        $locations = Location::orderBy('location_name')->get();

        return view('edit_chemical_batch', compact('chemical', 'batch', 'locations'));
    }

    public function batch_update(Request $request, $chemical_batch_id)
    {
        $batch = ChemicalBatch::with('chemical')->where('chemical_batch_id', $chemical_batch_id)->firstOrFail();
        $validated = $request->validate($this->batchRules($batch->chemical, $batch));
        $batch->update($validated);

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated chemical batch',
        ]);

        return redirect()->route('inventory.show', $batch->chemical_id)->with('success', __('messages.chemical.batch_updated'));
    }

    public function batch_delete($chemical_batch_id)
    {
        $batch = ChemicalBatch::where('chemical_batch_id', $chemical_batch_id)->firstOrFail();
        $chemicalId = $batch->chemical_id;
        $batch->delete();

        AuditLog::create([
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted chemical batch',
        ]);

        return redirect()->route('inventory.show', $chemicalId)->with('success', __('messages.chemical.batch_deleted'));
    }

    private function batchRules(Chemical $chemical, ?ChemicalBatch $batch = null): array
    {
        $batchNumber = Rule::unique('chemical_batches', 'batch_number')
            ->where(fn ($query) => $query->where('chemical_id', $chemical->chemical_id));

        if ($batch) {
            $batchNumber->ignore($batch->chemical_batch_id, 'chemical_batch_id');
        }

        return [
            'location_id' => 'required|integer|exists:locations,location_id',
            'batch_number' => ['required', 'string', 'max:255', $batchNumber],
            'brand_name' => 'required|string|max:255',
            'quantity_per_unit' => 'required|numeric|min:0|max:99999999.99',
            'quantity_received' => 'required|integer|min:1|max:99999999',
            'current_quantity' => 'required|integer|min:0|lte:quantity_received|max:99999999',
            'expiration_date' => 'required|date',
            'date_received' => 'required|date',
            'unit' => ['required', Rule::enum(Unit::class)],
        ];
    }

    public function use_edit($chemical_batch_id, ChemicalAlertService $alerts)
    {
        $batch = ChemicalBatch::with(['chemical', 'location'])->where('chemical_batch_id', $chemical_batch_id)->firstOrFail();

        if ($batch->current_quantity <= 0) {
            return back()->with('error', __('messages.chemical.out_of_stock'));
        }

        $expiration = $alerts->expirationStatus($batch);

        return view('use_chemical', compact('batch', 'expiration'));
    }

    public function use_update(Request $request, $chemical_batch_id)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Every click on a container logs exactly one use. The row is locked so
        // two people clicking at the same moment can't both take the last one.
        $remaining = DB::transaction(function () use ($chemical_batch_id, $validated) {
            $batch = ChemicalBatch::with('chemical')->where('chemical_batch_id', $chemical_batch_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($batch->current_quantity < 1) {
                return null;
            }

            $batch->current_quantity -= 1;
            $batch->save();

            AuditLog::create([
                'created_by' => Auth::user()['user_id'],
                'audit_action' => AuditAction::UPDATE,
                'target' => 'used chemical batch',
            ]);

            UsageLog::create([
                'created_by' => Auth::user()['user_id'],
                'location_id' => $batch->location_id,
                'item_type' => ItemType::CHEMICAL,
                'item_id' => $batch->chemical_batch_id,
                'quantity_remaining' => $batch->current_quantity,
                'notes' => $validated['notes'] ?? null,
            ]);

            return $batch->current_quantity;
        });

        if ($remaining === null) {
            return redirect()->route('inventory')->with('error', __('messages.chemical.out_of_stock'));
        }

        // Stay on the page so several containers can be logged in a row;
        // head back to the batch list once the last one is gone.
        $batch = ChemicalBatch::find($chemical_batch_id);
        $destination = $remaining > 0
            ? redirect()->route('inventory.use.edit', $chemical_batch_id)
            : redirect()->route('inventory.show', $batch?->chemical_id);

        return $destination->with('success', __('messages.chemical.used'));
    }
}
