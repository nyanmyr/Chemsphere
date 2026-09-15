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

        $query = AuditLog::query();

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('error', 'No usage log records found matching your range criteria.');
        }

        return view('audit_logs', compact('data', 'user'));
    }
}
