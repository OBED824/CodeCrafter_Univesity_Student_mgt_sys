<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Show the student registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('register');
    }

    /**
     * Register a brand-new student.
     *
     * This creates TWO linked records in one database transaction:
     *   1. a User (the login account) with the "student" role, and
     *   2. a Student profile (the academic record) linked to that user.
     * A student number is generated automatically. If anything fails,
     * the whole thing is rolled back so we never end up with a half
     * account. Finally the new student is logged straight in.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'program' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['first_name'].' '.$validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'], // hashed by the model cast
            ]);

            $user->assignRole('student');

            Student::create([
                'student_number' => $this->generateStudentNumber(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'program' => $validated['program'],
                'user_id' => $user->id,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')
            ->with('status', 'Account created successfully. Welcome!');
    }

    /**
     * Build the next sequential student number, e.g. SCS-214-0006-2026.
     * Loops (very rarely) so it never collides with an existing number.
     */
    private function generateStudentNumber(): string
    {
        $sequence = Student::count() + 1;

        do {
            $number = sprintf('SCS-214-%04d-%d', $sequence, (int) date('Y'));
            $sequence++;
        } while (Student::where('student_number', $number)->exists());

        return $number;
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
