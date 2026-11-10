<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'lecturer'])
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['administrator', 'lecturer']))
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['administrator', 'lecturer'])],
            'staff_number' => ['required_if:role,lecturer', 'nullable', 'string', 'max:50', 'unique:lecturers,staff_number'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ];

        if ($request->input('role') === 'lecturer') {
            $rules['email'][] = Rule::unique('lecturers', 'email');
        }

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['first_name'].' '.$validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->syncRoles([$validated['role']]);

            if ($validated['role'] === 'lecturer') {
                Lecturer::create([
                    'staff_number' => $validated['staff_number'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'user_id' => $user->id,
                ]);
            }

            return $user;
        });

        return redirect()->route('admin.users.index')
            ->with('status', ucfirst($validated['role']).' account created for '.$user->name.'.');
    }
}
