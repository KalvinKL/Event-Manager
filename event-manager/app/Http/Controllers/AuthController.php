<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const VERIFICATION_RESEND_DELAY_SECONDS = 60;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'attendee',
        ]);

        event(new Registered($user));
        RateLimiter::hit('verification-resend:'.$user->getKey(), self::VERIFICATION_RESEND_DELAY_SECONDS);
        $request->session()->put(
            'verification_resend_available_at',
            now()->addSeconds(self::VERIFICATION_RESEND_DELAY_SECONDS)->timestamp
        );
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'A verification link has been sent to your email address.');
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();
        $rateLimitKey = 'verification-resend:'.$user->getAuthIdentifier();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $request->session()->put(
                'verification_resend_available_at',
                now()->addSeconds(RateLimiter::availableIn($rateLimitKey))->timestamp
            );

            return back()->with('status', 'verification-link-cooldown');
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($rateLimitKey, self::VERIFICATION_RESEND_DELAY_SECONDS);
        $request->session()->put(
            'verification_resend_available_at',
            now()->addSeconds(self::VERIFICATION_RESEND_DELAY_SECONDS)->timestamp
        );

        return back()->with('status', 'verification-link-sent');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out.');
    }
}
