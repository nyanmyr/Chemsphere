<?php

namespace App\Http\Controllers;

use App\AuditAction;
use App\Models\AuditLog;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function users()
    {
        $data = DB::table('users')->get();
        $user = Auth::user();
        return view('users', ['data' => $data, 'user' => $user]);
    }

    public function edit($user_id)
    {
        if (Auth::user()['user_id'] == $user_id) {
            return redirect()->back(fallback: route('welcome'))->withErrors(['id' => 'Cannot edit own role.']);
        }

        $user = User::where(
            'user_id',
            $user_id
        )->firstOrFail();

        return view('edit_user', compact('user'));
    }

    public function update(Request $request, $user_id)
    {
        $validated = $request->validate([
            'user_role' => ['required', Rule::enum(UserRole::class)]
        ]);

        $user = User::where(
            'user_id',
            $user_id
        )->firstOrFail();

        $user->update($validated);

        AuditLog::create([
            'user_id' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated user role',
        ]);

        return redirect()->route('users')->with('success', 'Location updated successfully');
    }
}
