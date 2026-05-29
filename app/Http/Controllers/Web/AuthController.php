<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('customer.login');
    }

    public function showRegister()
    {
        return view('customer.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);

        $token = $user->createToken('web-token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->intended(route('menu'))->with('success', 'Register berhasil!');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $token = Auth::user()->createToken('web-token')->plainTextToken;
            session(['api_token' => $token]);

            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->intended('/admin');
            }
            if ($user->isKasir()) {
                return redirect()->intended('/kasir');
            }

            return redirect()->intended(route('menu'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::user()->tokens()->where('name', 'web-token')->delete();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return redirect()->away(Socialite::driver('google')->redirect()->getTargetUrl());
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google.']);
        }

        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'role' => 'customer',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $token = $user->createToken('web-token')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->intended(route('menu'));
    }
}
