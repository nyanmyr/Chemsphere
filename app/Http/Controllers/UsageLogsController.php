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

        $query = UsageLog::query();

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('error', 'No usage log records found matching your range criteria.');
        }

        return view('usage_logs', compact('data', 'user'));
    }
}
