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
        $data = $query->get();

        return view('usage_logs', compact('data', 'user'));
    }
}
