<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Handle standard registration (Email & Password only)
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        Auth::login($user);

        return redirect('/');
    }

    // Handle standard login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Redirect user to Google OAuth page
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle incoming OAuth callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(), // Captured from Google if available
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(24)), // Dummy password for SSO users
            ]);

            Auth::login($user);

            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Google authentication failed.']);
        }
    }
}
