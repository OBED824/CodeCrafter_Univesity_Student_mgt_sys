<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('login');
    }

    /**
     * Handle a login attempt.
     *
     * 1. Validate that email + password were submitted.
     * 2. Auth::attempt checks the credentials against the users table
     *    (Laravel hashes the input and compares it to the stored hash).
     * 3. On success the session ID is regenerated (prevents session
     *    fixation attacks) and the user is sent to their dashboard.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended($this->dashboardUrlFor(Auth::user()))
                ->with('status', 'Welcome back, '.Auth::user()->name.'!');
        }

        // Failed login: show a clear error under the email field.
        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out and wipe the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')->with('status', 'You have been logged out.');
    }

    /**
     * Where each role lands after login.
     */
    private function dashboardUrlFor(User $user): string
    {
        return match (true) {
            $user->hasRole('administrator') => route('admin.dashboard'),
            $user->hasRole('lecturer') => route('lecturer.dashboard'),
            $user->hasRole('student') => route('student.dashboard'),
            default => route('index'),
        };
    }
}
