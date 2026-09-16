<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogsController extends Controller
{
    public function auditLogs(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_audit_log_id_min' => 'nullable|integer|min:1',
            'search_audit_log_id_max' => 'nullable|integer|min:1',
            'search_created_by_min' => 'nullable|integer|min:1',
            'search_created_by_max' => 'nullable|integer|min:1',
            'search_audit_action' => 'nullable|array',
        ]);

        $query = AuditLog::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->where('target', 'LIKE', "%{$request->search}%");
            });
        });

        $query->when($request->filled('search_audit_log_id_min'), function ($q) use ($request) {
            $q->where('audit_log_id', '>=', $request->search_audit_log_id_min);
        });

        $query->when($request->filled('search_audit_log_id_max'), function ($q) use ($request) {
            $q->where('audit_log_id', '<=', $request->search_audit_log_id_max);
        });

        $query->when($request->filled('search_created_by_min'), function ($q) use ($request) {
            $q->where('created_by', '>=', $request->search_created_by_min);
        });

        $query->when($request->filled('search_created_by_max'), function ($q) use ($request) {
            $q->where('created_by', '<=', $request->search_created_by_max);
        });

        $query->when($request->filled('search_audit_action'), function ($q) use ($request) {
            $q->whereIn('audit_action', (array) $request->search_audit_action);
        });

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('error', 'No usage log records found matching your range criteria.');
        }

        return view('audit_logs', compact('data', 'user'));
    }
}
