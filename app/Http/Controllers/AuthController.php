<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
            'user_role' => 'pending'
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
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        $user = Auth::user();

        if ($user->user_role === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Your account is currently pending approval.']);
        }

        if (is_null($user->google_id)) {
            $userId = $user->id;

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $request->session()->put('linking_user_id', $userId);

            return Socialite::driver('google')->redirect();
        }

        $request->session()->regenerate();
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        // stateless() should be removed for production
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $linkingUserId = session('linking_user_id');

            if ($linkingUserId) {
                session()->forget('linking_user_id');

                $user = User::find($linkingUserId);

                if (!$user) {
                    return redirect('/login')->withErrors(['email' => 'Account not found.']);
                }

                if ($user->email !== $googleUser->getEmail()) {
                    return redirect('/login')->withErrors([
                        'email' => "Email mismatch! You tried signing into Google with '{$googleUser->getEmail()}', but your account is registered as '{$user->email}'."
                    ]);
                }

                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);

                Auth::login($user);
                return redirect('/');
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect('/login')->withErrors(['email' => 'No account found with this email. Please register first.']);
            }

            if ($user->user_role === 'pending') {
                return redirect('/login')->withErrors(['email' => 'Your account is currently pending approval.']);
            }

            if (is_null($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }

            Auth::login($user);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Google authentication failed.']);
        }
    }
}
