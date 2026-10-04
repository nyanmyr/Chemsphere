<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Enums\AuditAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|unique:users,email|ends_with:@uic.edu.ph',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'user_role' => 'pending'
        ]);

        AuditLog::create([
            'created_by' => $user['user_id'],
            'audit_action' => AuditAction::REGISTER,
            'target' => 'user registered',
        ]);

        return redirect('/pending');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->with('error', __('messages.auth.invalid_credentials'));
        }

        $user = Auth::user();

        if ($user['user_role'] === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', __('messages.auth.pending'));
        }

        if ($user['google_id'] == null) {
            $userId = $user['user_id'];

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $request->session()->put('linking_user_id', $userId);

            return Socialite::driver('google')->redirect();
        }

        $request->session()->regenerate();

        AuditLog::create([
            'created_by' => $user['user_id'],
            'audit_action' => AuditAction::LOGIN,
            'target' => 'user logged in',
        ]);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        AuditLog::create([
            'created_by' => $user['user_id'],
            'audit_action' => AuditAction::LOGOUT,
            'target' => 'user logged out',
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        // stateless() should be removed for production
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth failed', ['exception' => $e]);
            return redirect('/login')->with('error', __('messages.auth.google_failed'));
        }

        $linkingUserId = $request->session()->pull('linking_user_id');

        if ($linkingUserId) {
            $user = User::find($linkingUserId);

            if (!$user) {
                return redirect('/login')->with('error', __('messages.auth.account_not_found'));
            }

            if (Str::lower($user->email) !== Str::lower($googleUser->getEmail())) {
                return redirect('/login')->with('error', __('messages.auth.google_email_mismatch', [
                    'google' => $googleUser->getEmail(),
                    'account' => $user->email,
                ]));
            }

            if ($user->google_id !== null && $user->google_id !== $googleUser->getId()) {
                return redirect('/login')->with('error', __('messages.auth.google_already_linked'));
            }

            $user->google_id = $googleUser->getId();
            $user->save();
        } else {
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect('/login')->with('error', __('messages.auth.google_no_account'));
            }

            if ($user->user_role?->isRole(UserRole::PENDING)) {
                return redirect('/login')->with('error', __('messages.auth.pending'));
            }

            if ($user->google_id === null) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        }

        return $this->finishLogin($request, $user);
    }

    private function finishLogin(Request $request, User $user)
    {
        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::create([
            'created_by' => $user->user_id,
            'audit_action' => AuditAction::LOGIN,
            'target' => 'user logged in',
        ]);

        return redirect('/');
    }
}
