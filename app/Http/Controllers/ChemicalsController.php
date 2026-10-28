<?php

namespace App\Http\Controllers;

use App\Enums\AlertType;
use App\Enums\AuditAction;
use App\Enums\GHSSymbol;
use App\Enums\SafetyClass;
use App\Enums\Unit;
use App\Models\AuditLog;
use App\Models\Chemical;
use App\Models\ChemicalContainer;
use App\Models\Location;
use App\Models\UsageLog;
use App\Services\ChemicalContainerAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChemicalsController extends Controller
{
    public function index(Request $request, ChemicalContainerAlertService $alerts)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'search_chemical_id_min' => 'nullable|integer|min:1',
            'search_chemical_id_max' => 'nullable|integer|min:1',
            'search_location_id_min' => 'nullable|integer|min:1',
            'search_location_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
            'search_quantity_per_unit_min' => 'nullable|numeric|min:0|max:99999999.99',
            'search_quantity_per_unit_max' => 'nullable|numeric|min:0|max:99999999.99',
            'search_quantity_received_min' => 'nullable|integer|min:0|max:99999999',
            'search_quantity_received_max' => 'nullable|integer|min:0|max:99999999',
            'search_current_quantity_min' => 'nullable|integer|min:0|max:99999999',
            'search_current_quantity_max' => 'nullable|integer|min:0|max:99999999',
            'search_expiration_date_min' => 'nullable|date',
            'search_expiration_date_max' => 'nullable|date',
            'search_date_received_min' => 'nullable|date',
            'search_date_received_max' => 'nullable|date',
            'search_safety_classes' => 'nullable|array',
            'search_safety_classes.*' => ['string', Rule::enum(SafetyClass::class)],
            'search_ghs_symbols' => 'nullable|array',
            'search_ghs_symbols.*' => ['string', Rule::enum(GHSSymbol::class)],
            'search_unit' => 'nullable|array',
            'search_unit.*' => ['string', Rule::enum(Unit::class)],
            'search_alert_type' => 'nullable|array',
            'search_alert_type.*' => ['string', Rule::enum(AlertType::class)->only([
                AlertType::EXPIRING_SOON, AlertType::EXPIRED, AlertType::LOW_STOCK, AlertType::OUT_OF_STOCK,
            ])],
            'sort' => 'nullable|in:recent,oldest,alphabetical',
        ]);

        $query = Chemical::query()->with(['containers.location'])->withCount('containers');

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(function ($searchQuery) use ($term) {
                $searchQuery->where('chemical_name', 'like', "%{$term}%")
                    ->orWhereHas('containers', fn ($containerQuery) => $containerQuery
                        ->where('batch_number', 'like', "%{$term}%")
                        ->orWhere('brand_name', 'like', "%{$term}%"));
                if (is_numeric($term)) $searchQuery->orWhere('chemical_id', '=', (int) $term);
            });
        }

        foreach (['chemical_id', 'created_by'] as $field) {
            foreach (['min' => '>=', 'max' => '<='] as $bound => $operator) {
                $key = "search_{$field}_{$bound}";
                if ($request->filled($key)) {
                    $query->where($field, $operator, $request->input($key));
                }
            }
        }

        $containerFilterFields = ['location_id', 'quantity_received', 'current_quantity', 'expiration_date', 'date_received'];
        $hasContainerRange = collect($containerFilterFields)->contains(fn ($field) =>
            $request->filled("search_{$field}_min") || $request->filled("search_{$field}_max")
        );
        if ($hasContainerRange) {
            $query->whereHas('containers', function ($containerQuery) use ($request, $containerFilterFields) {
                foreach ($containerFilterFields as $field) {
                    foreach (['min' => '>=', 'max' => '<='] as $bound => $operator) {
                        $key = "search_{$field}_{$bound}";
                        if (!$request->filled($key)) continue;
                        $value = $request->input($key);
                        if (in_array($field, ['expiration_date', 'date_received'], true)) {
                            $containerQuery->whereDate($field, $operator, $value);
                        } else {
                            $containerQuery->where($field, $operator, $value);
                        }
                    }
                }
            });
        }

        foreach (['min' => '>=', 'max' => '<='] as $bound => $operator) {
            $key = "search_quantity_per_unit_{$bound}";
            if ($request->filled($key)) {
                $query->where('quantity_per_unit', $operator, $request->input($key));
            }
        }

        if ($request->filled('search_safety_classes')) {
            $classes = $request->input('search_safety_classes');
            $query->where(function ($sub) use ($classes) {
                foreach ($classes as $class) $sub->orWhereRaw('FIND_IN_SET(?, safety_classes)', [$class]);
            });
        }
        if ($request->filled('search_ghs_symbols')) {
            $symbols = $request->input('search_ghs_symbols');
            $query->where(function ($sub) use ($symbols) {
                foreach ($symbols as $symbol) $sub->orWhereRaw('FIND_IN_SET(?, ghs_symbols)', [$symbol]);
            });
        }
        if ($request->filled('search_unit')) {
            $query->whereIn('unit', $request->input('search_unit'));
        }
        if ($request->filled('search_alert_type')) {
            $types = collect($request->input('search_alert_type'))->map(fn ($value) => AlertType::from($value));
            $query->whereHas('containers', function ($containerQuery) use ($types, $alerts) {
                $containerQuery->where(function ($conditions) use ($types, $alerts) {
                    foreach ($types as $type) {
                        $conditions->orWhere(function ($condition) use ($type, $alerts) {
                            match ($type) {
                                AlertType::EXPIRED => $condition->whereDate('expiration_date', '<=', today()),
                                AlertType::EXPIRING_SOON => $condition->whereDate('expiration_date', '>', today())
                                    ->whereDate('expiration_date', '<=', today()->addMonthsNoOverflow(3)),
                                AlertType::OUT_OF_STOCK => $condition->where('current_quantity', '<=', 0),
                                AlertType::LOW_STOCK => $condition->where('current_quantity', '>', 0)
                                    ->whereRaw('current_quantity <= quantity_received * ?', [$alerts->lowStockThreshold()]),
                                default => $condition->whereRaw('1 = 0'),
                            };
                        });
                    }
                });
            });
        }

        $sort = $request->input('sort', 'recent');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc')->orderBy('chemical_id', 'asc');
        } elseif ($sort === 'alphabetical') {
            $query->orderBy('chemical_name', 'asc')->orderBy('chemical_id', 'asc');
        } else {
            $query->orderBy('created_at', 'desc')->orderBy('chemical_id', 'desc');
        }

        $data = $query->paginate(10)->withQueryString();
        $statuses = [];
        foreach ($data as $chemical) {
            $statuses[$chemical->chemical_id] = $alerts->chemicalStatuses($chemical);
        }

        $alertTypes = [AlertType::EXPIRING_SOON, AlertType::EXPIRED, AlertType::LOW_STOCK, AlertType::OUT_OF_STOCK];
        return view('inventory', compact('data', 'statuses', 'alertTypes'));
    }

    public function create()
    {
        $safetyClasses = SafetyClass::cases();
        $ghsSymbols = GHSSymbol::cases();
        $units = Unit::cases();
        return view('chemical.create', compact('safetyClasses', 'ghsSymbols', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateChemical($request);
        $validated['safety_classes'] = implode(',', $validated['safety_classes'] ?? []);
        $validated['ghs_symbols'] = implode(',', $validated['ghs_symbols'] ?? []);
        $validated['created_by'] = Auth::user()->user_id;

        // Reuse an existing chemical for an exact same chemical.
        $existing = Chemical::query()
            ->whereRaw('LOWER(chemical_name) = ?', [mb_strtolower(trim($validated['chemical_name']))])
            ->first();
        $chemical = $existing ?? Chemical::create($validated);

        AuditLog::create(['created_by' => Auth::user()->user_id, 'audit_action' => $existing ? AuditAction::UPDATE : AuditAction::CREATE, 'target' => $existing ? 'reused chemical' : 'created chemical']);
        return redirect()->route('inventory.show', $chemical->chemical_id)
            ->with('success', $existing ? 'That chemical already existed, so its chemical was reused. Add a container below.' : 'Chemical created. Add its first container below.');
    }

    public function show(Chemical $chemical, ChemicalContainerAlertService $alerts)
    {
        $chemical->load(['containers.location', 'containers.user']);
        $locations = Location::orderBy('location_name')->get();
        $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
        $containerStatuses = [];
        foreach ($chemical->containers as $container) {
            $containerStatuses[$container->chemical_container_id] = $alerts->expirationStatus($container);
        }
        return view('chemical.show', compact('chemical', 'locations', 'isAdmin', 'containerStatuses'));
    }

    public function edit(Chemical $chemical)
    {
        $safetyClasses = SafetyClass::cases();
        $ghsSymbols = GHSSymbol::cases();
        $units = Unit::cases();
        return view('chemical.edit', compact('chemical', 'safetyClasses', 'ghsSymbols', 'units'));
    }

    public function update(Request $request, Chemical $chemical)
    {
        $requestedName = mb_strtolower(trim((string) $request->input('chemical_name', '')));
        $duplicate = Chemical::query()
            ->whereRaw('LOWER(chemical_name) = ?', [$requestedName])
            ->where('chemical_id', '!=', $chemical->chemical_id)
            ->exists();
        if ($duplicate) {
            return back()->withErrors(['chemical_name' => 'A chemical with this chemical name already exists. Edit that entry instead.'])->withInput();
        }
        $validated = $this->validateChemical($request);
        $validated['safety_classes'] = implode(',', $validated['safety_classes'] ?? []);
        $validated['ghs_symbols'] = implode(',', $validated['ghs_symbols'] ?? []);
        $chemical->update($validated);
        AuditLog::create(['created_by' => Auth::user()->user_id, 'audit_action' => AuditAction::UPDATE, 'target' => 'updated chemical']);
        return redirect()->route('inventory.show', $chemical->chemical_id)->with('success', 'Chemical updated.');
    }

    public function storeContainer(Request $request, Chemical $chemical, ChemicalContainerAlertService $alerts)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,location_id',
            'batch_number' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'quantity_received' => 'required|integer|min:0|max:99999999',
            'expiration_date' => 'required|date',
            'date_received' => 'required|date',
        ]);
        $validated['current_quantity'] = $validated['quantity_received'];
        $validated['chemical_id'] = $chemical->chemical_id;
        $validated['created_by'] = Auth::user()->user_id;
        $container = ChemicalContainer::create($validated);
        $alerts->checkContainer($container->fresh('chemical'));
        AuditLog::create(['created_by' => Auth::user()->user_id, 'audit_action' => AuditAction::CREATE, 'target' => 'received chemical container']);
        return redirect()->route('inventory.show', $chemical->chemical_id)->with('success', 'Container / batch added.');
    }

    public function editContainer(ChemicalContainer $container)
    {
        $container->load('chemical');
        $locations = Location::orderBy('location_name')->get();
        return view('chemical.edit-container', compact('container', 'locations'));
    }

    public function updateContainer(Request $request, ChemicalContainer $container, ChemicalContainerAlertService $alerts)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,location_id',
            'batch_number' => 'required|string|max:255', 'brand_name' => 'required|string|max:255',
            'quantity_received' => 'required|integer|min:0|max:99999999',
            'current_quantity' => 'required|integer|min:0|max:99999999|lte:quantity_received',
            'expiration_date' => 'required|date', 'date_received' => 'required|date',
        ]);
        $container->update($validated);
        $container->refresh()->load('chemical');
        $alerts->checkContainer($container);
        AuditLog::create(['created_by' => Auth::user()->user_id, 'audit_action' => AuditAction::UPDATE, 'target' => 'updated chemical container']);
        return redirect()->route('inventory.show', $container->chemical_id)->with('success', 'Container updated.');
    }

    public function destroyContainer(ChemicalContainer $container)
    {
        if ($container->usageLogs()->exists()) {
            return back()->with('error', 'This batch has usage history and cannot be deleted. Keep it for audit purposes; you can edit the batch details instead.');
        }

        $chemicalId = $container->chemical_id;
        $batchNumber = $container->batch_number;
        $container->delete();
        AuditLog::create([
            'created_by' => Auth::user()->user_id,
            'audit_action' => AuditAction::DELETE,
            'target' => 'deleted chemical batch ' . $batchNumber,
        ]);

        return redirect()->route('inventory.show', $chemicalId)->with('success', 'Batch ' . $batchNumber . ' deleted.');
    }

    public function useContainer(Request $request, ChemicalContainer $container, ChemicalContainerAlertService $alerts)
    {
        $validated = $request->validate([
            'selected_containers' => 'required|array|min:1|max:100',
            'selected_containers.*' => 'required|integer|min:1|distinct',
            'notes' => 'nullable|string|max:5000',
        ]);
        $selectedCount = count($validated['selected_containers']);
        $remaining = DB::transaction(function () use ($container, $validated, $selectedCount) {
            $locked = ChemicalContainer::whereKey($container->getKey())->lockForUpdate()->firstOrFail();
            if ($locked->current_quantity < $selectedCount) return null;

            for ($i = 0; $i < $selectedCount; $i++) {
                $locked->decrement('current_quantity');
                $locked->refresh();
                UsageLog::create([
                    'created_by' => Auth::user()->user_id,
                    'location_id' => $locked->location_id,
                    'item_type' => 'chemical',
                    'item_id' => $locked->chemical_id ?? $locked->chemical_container_id,
                    'chemical_container_id' => $locked->chemical_container_id,
                    'quantity_remaining' => $locked->current_quantity,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            AuditLog::create([
                'created_by' => Auth::user()->user_id,
                'audit_action' => AuditAction::UPDATE,
                'target' => 'used '.$selectedCount.' chemical container(s)',
            ]);
            return $locked->current_quantity;
        });
        if ($remaining === null) return back()->with('error', 'The batch no longer has enough containers for your selection. Please select again.');
        $alerts->checkContainer($container->fresh('chemical'));
        return redirect()->route('inventory.show', $container->chemical_id)->with('success', $selectedCount.' container'.($selectedCount === 1 ? '' : 's').' use logged. Remaining in this batch: '.$remaining.'.');
    }

    public function destroy(Chemical $chemical)
    {
        if ($chemical->containers()->where(function ($query) {
            $query->whereHas('usageLogs')->orWhereHas('alerts');
        })->exists()) {
            return back()->with('error', 'This chemical has usage or alert history and cannot be deleted. You can leave it in the chemical instead.');
        }
        $chemical->delete();
        AuditLog::create(['created_by' => Auth::user()->user_id, 'audit_action' => AuditAction::DELETE, 'target' => 'deleted chemical']);
        return redirect()->route('inventory')->with('success', 'Chemical deleted.');
    }

    private function validateChemical(Request $request): array
    {
        return $request->validate([
            'chemical_name' => 'required|string|max:255',
            'quantity_per_unit' => 'required|numeric|min:0|max:99999999.99',
            'safety_classes' => 'nullable|array', 'safety_classes.*' => ['required', Rule::enum(SafetyClass::class)],
            'ghs_symbols' => 'nullable|array', 'ghs_symbols.*' => ['required', Rule::enum(GHSSymbol::class)],
            'unit' => ['required', Rule::enum(Unit::class)],
        ]);
    }
}
