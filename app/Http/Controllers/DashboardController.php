<?php

namespace App\Http\Controllers;

use App\Enums\ItemType;
use App\Enums\UserRole;
use App\Models\Chemical;
use App\Models\UsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function welcome()
    {
        $user = Auth::user();

        // Guests and not-yet-approved accounts only get the plain welcome page.
        // (The "/" route has no 'pending' middleware, so this check has to live here.)
        if (! in_array($user?->user_role, [UserRole::USER, UserRole::ADMIN], true)) {
            return view('welcome');
        }

        // "Today" is the user's calendar day, not the database's (UTC) day.
        $displayZone = config('app.display_timezone');
        $storageZone = config('app.timezone');

        $today = now($displayZone)->startOfDay();
        $from = $today->copy()->setTimezone($storageZone);
        $to = $today->copy()->addDay()->setTimezone($storageZone);

        $usage = UsageLog::query()
            ->where('item_type', ItemType::CHEMICAL->value)
            ->where('quantity_used', '>', 0)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<', $to)
            ->selectRaw('item_id, SUM(quantity_used) as total_used, COUNT(*) as times_used, MAX(created_at) as last_used_at')
            ->groupBy('item_id')
            ->orderByDesc('last_used_at')
            ->get();

        $chemicals = Chemical::whereIn('chemical_id', $usage->pluck('item_id'))
            ->get()
            ->keyBy('chemical_id');

        $usedToday = $usage->map(fn ($row) => [
            'id' => $row->item_id,
            'chemical' => $chemicals->get($row->item_id),
            'total_used' => $row->total_used,
            'times_used' => (int) $row->times_used,
            'last_used_at' => Carbon::parse($row->last_used_at, $storageZone)->setTimezone($displayZone),
        ]);

        return view('welcome', compact('usedToday', 'today'));
    }
}
