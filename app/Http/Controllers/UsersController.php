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
    public function users(Request $request)
    {
        $user = Auth::user();

        $validate = $request->validate([
            'search' => 'nullable|string|max:255',
            'search_user_id_min' => 'nullable|integer|min:1',
            'search_user_id_max' => 'nullable|integer|min:1',
            'search_user_role' => 'nullable|array',
        ]);

        $query = User::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->where('email', 'LIKE', "%{$request->search}%");
            });
        });

        $query->when($request->filled('search_user_id_min'), function ($q) use ($request) {
            $q->where('user_id', '>=', $request->search_user_id_min);
        });

        $query->when($request->filled('search_user_id_max'), function ($q) use ($request) {
            $q->where('user_id', '<=', $request->search_user_id_max);
        });

        $query->when($request->filled('search_user_role'), function ($q) use ($request) {
            $q->whereIn('user_role', (array) $request->search_user_role);
        });

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->now('error', 'No location records found matching your range criteria.');
        }

        $search = $request->input('search');

        return view('users', compact('data', 'user'));
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
            'created_by' => Auth::user()['user_id'],
            'audit_action' => AuditAction::UPDATE,
            'target' => 'updated user role',
        ]);

        return redirect()->route('users')->with('success', 'Location updated successfully');
    }
}
