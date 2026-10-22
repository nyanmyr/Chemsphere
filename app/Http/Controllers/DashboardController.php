<?php

namespace App\Http\Controllers;

use App\Enums\AlertType;
use App\Enums\ItemType;
use App\Enums\UserRole;
use App\Models\Chemical;
use App\Models\UsageLog;
use App\Services\ChemicalAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    private const ATTENTION_TYPES = [
        AlertType::EXPIRED,
        AlertType::LOW_STOCK,
        AlertType::OUT_OF_STOCK,
    ];

    public function welcome(Request $request, ChemicalAlertService $alerts)
    {
        $user = Auth::user();

        if (! in_array($user?->user_role, [UserRole::USER, UserRole::ADMIN], true)) {
            return view('welcome');
        }

        // "Today" is the user's calendar day, not the database's (UTC) day.
        $today = now(config('app.display_timezone'))->startOfDay();

        return view('welcome', [
            'today' => $today,
            'usedToday' => $this->usedToday($today),
            'attention' => $this->needsAttention($request, $alerts->lowStockThreshold()),
            'alertTypes' => self::ATTENTION_TYPES,
        ]);
    }

    private function usedToday(Carbon $today)
    {
        $displayZone = config('app.display_timezone');
        $storageZone = config('app.timezone');

        $from = $today->copy()->setTimezone($storageZone);
        $to = $today->copy()->addDay()->setTimezone($storageZone);

        $usage = UsageLog::query()
            ->where('item_type', ItemType::CHEMICAL->value)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<', $to)
            ->selectRaw('item_id, COUNT(*) as times_used, MAX(created_at) as last_used_at')
            ->groupBy('item_id')
            ->orderByDesc('last_used_at')
            ->get();

        $chemicals = Chemical::whereIn('chemical_id', $usage->pluck('item_id'))
            ->get()
            ->keyBy('chemical_id');

        return $usage->map(fn ($row) => [
            'id' => $row->item_id,
            'chemical' => $chemicals->get($row->item_id),
            'total_used' => $row->total_used,
            'times_used' => (int) $row->times_used,
            'last_used_at' => Carbon::parse($row->last_used_at, $storageZone)->setTimezone($displayZone),
        ]);
    }

    private function needsAttention(Request $request, float $lowStockThreshold)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_alert_type' => 'nullable|array',
            'search_alert_type.*' => ['required', Rule::enum(AlertType::class)->only(self::ATTENTION_TYPES)],
        ]);

        $selected = collect($validated['search_alert_type'] ?? [])
            ->map(fn ($value) => AlertType::from($value))
            ->all();
        $types = $selected ?: self::ATTENTION_TYPES;

        $query = Chemical::query()->with('location');

        $query->where(function ($q) use ($types, $lowStockThreshold) {
            foreach ($types as $type) {
                $q->orWhere(fn ($sub) => $this->applyCondition($sub, $type, $lowStockThreshold));
            }
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('chemical_name', 'LIKE', "%{$request['search']}%")
                    ->orWhere('batch_number', 'LIKE', "%{$request['search']}%")
                    ->orWhere('brand_name', 'LIKE', "%{$request['search']}%");
            });
        });

        $query->orderBy('expiration_date')->orderBy('chemical_id');

        return $query->paginate(10)
            ->withQueryString()
            ->through(fn (Chemical $c) => [
                'chemical' => $c,
                'statuses' => $this->statusesOf($c, $lowStockThreshold),
            ]);
    }

    private function applyCondition($query, AlertType $type, float $lowStockThreshold): void
    {
        match ($type) {
            AlertType::EXPIRED => $query->where('expiration_date', '<', now()),
            AlertType::OUT_OF_STOCK => $query->where('current_quantity', '<=', 0),
            AlertType::LOW_STOCK => $query
                ->where('current_quantity', '>', 0)
                ->whereRaw('current_quantity <= quantity_received * ?', [$lowStockThreshold]),
        };
    }

    private function statusesOf(Chemical $chemical, float $lowStockThreshold): array
    {
        $current = (float) $chemical->current_quantity;
        $statuses = [];

        if ($chemical->expiration_date->isPast()) {
            $statuses[] = AlertType::EXPIRED;
        }

        if ($current <= 0) {
            $statuses[] = AlertType::OUT_OF_STOCK;
        } elseif ($current <= (float) $chemical->quantity_received * $lowStockThreshold) {
            $statuses[] = AlertType::LOW_STOCK;
        }

        return $statuses;
    }
}
