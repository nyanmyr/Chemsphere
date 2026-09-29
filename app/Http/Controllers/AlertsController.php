<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertsController extends Controller
{
    public function alerts(Request $request)
    {
        $user = Auth::user();

        $query = $user->alerts()->with('chemical');
        $query->latest();

        $data = $query->paginate(10)->withQueryString();

        if ($data->isEmpty()) {
            session()->now('info', __('messages.alert.none_found'));
        }

        return view('alerts', compact('data', 'user'));
    }

    public function markAsRead(Alert $alert)
    {
        abort_unless(
            $alert->notifiable_id === Auth::id()
                && $alert->notifiable_type === Auth::user()->getMorphClass(),
            403
        );

        $alert->markAsRead();

        return back();
    }
}
