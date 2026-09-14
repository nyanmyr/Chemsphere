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
        $data = $query->get();

        return view('audit_logs', compact('data', 'user'));
    }
}
