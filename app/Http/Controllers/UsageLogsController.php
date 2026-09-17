<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsageLogsController extends Controller
{
    public function usageLogs(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_usage_log_id_min' => 'nullable|integer|min:1',
            'search_usage_log_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
            'search_location_id_min' => 'nullable|integer|min:1',
            'search_location_id_max' => 'nullable|integer|min:1',
            'search_item_type' => 'nullable|array',
            'search_item_id_min' => 'nullable|integer|min:1',
            'search_item_id_max' => 'nullable|integer|min:1',
            'search_quantity_used_min' => 'nullable|numeric|min:0|max:9999999999.999',
            'search_quantity_used_max' => 'nullable|numeric|min:0|max:9999999999.999',
            'search_quantity_remaining_min' => 'nullable|numeric|min:0|max:9999999999.999',
            'search_quantity_remaining_max' => 'nullable|numeric|min:0|max:9999999999.999',
        ]);

        $query = UsageLog::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->where('notes', 'LIKE', "%{$request['search']}%");
            });
        });

        $query->when($request->filled('search_usage_log_id_min'), function ($q) use ($request) {
            $q->where('usage_log_id', '>=', $request['search_usage_log_id_min']);
        });

        $query->when($request->filled('search_usage_log_id_max'), function ($q) use ($request) {
            $q->where('usage_log_id', '<=', $request['search_usage_log_id_max']);
        });

        $query->when($request->filled('search_created_by_min'), function ($q) use ($request) {
            $q->where('created_by', '>=', $request['search_created_by_min']);
        });

        $query->when($request->filled('search_created_by_max'), function ($q) use ($request) {
            $q->where('created_by', '<=', $request['search_created_by_max']);
        });

        $query->when($request->filled('search_location_id_min'), function ($q) use ($request) {
            $q->where('location_id', '>=', $request['search_location_id_min']);
        });

        $query->when($request->filled('search_location_id_max'), function ($q) use ($request) {
            $q->where('location_id', '<=', $request['search_location_id_max']);
        });

        $query->when($request->filled('search_item_type'), function ($q) use ($request) {
            $q->whereIn('item_type', (array) $request['search_item_type']);
        });

        $query->when($request->filled('search_item_id_min'), function ($q) use ($request) {
            $q->where('item_id', '>=', $request['search_item_id_min']);
        });

        $query->when($request->filled('search_item_id_max'), function ($q) use ($request) {
            $q->where('item_id', '<=', $request['search_item_id_max']);
        });

        $query->when($request->filled('search_quantity_used_min'), function ($q) use ($request) {
            $q->where('quantity_used', '>=', $request['search_quantity_used_min']);
        });

        $query->when($request->filled('search_quantity_used_max'), function ($q) use ($request) {
            $q->where('quantity_used', '<=', $request['search_quantity_used_max']);
        });

        $query->when($request->filled('search_quantity_remaining_min'), function ($q) use ($request) {
            $q->where('quantity_remaining', '>=', $request['search_quantity_remaining_min']);
        });

        $query->when($request->filled('search_quantity_remaining_max'), function ($q) use ($request) {
            $q->where('quantity_remaining', '<=', $request['search_quantity_remaining_max']);
        });

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('error', 'No usage log records found matching your range criteria.');
        }

        return view('usage_logs', compact('data', 'user'));
    }
}
