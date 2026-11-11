<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'lecturer', 'student'])
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'students' => Student::query()
                ->whereNull('user_id')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load(['roles', 'lecturer', 'student']),
            'students' => Student::query()
                ->whereNull('user_id')
                ->orWhere('user_id', $user->id)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $selectedStudent = $request->input('role') === 'student'
            ? Student::query()->find($request->integer('student_id'))
            : null;
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['administrator', 'lecturer', 'student'])],
            'staff_number' => ['required_if:role,lecturer', 'nullable', 'string', 'max:50', Rule::unique('lecturers', 'staff_number')->ignore($user->lecturer?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'student_id' => [
                'required_if:role,student',
                'nullable',
                'integer',
                Rule::exists('students', 'id'),
                Rule::unique('students', 'user_id')->ignore($user->student?->id),
            ],
        ];
        if ($request->input('role') === 'lecturer') {
            $rules['email'][] = Rule::unique('lecturers', 'email')->ignore($user->lecturer?->id);
        }
        if ($request->input('role') === 'student') {
            $rules['email'][] = Rule::unique('students', 'email')->ignore($selectedStudent?->id);
        }
        $validated = $request->validate($rules);

        if ($user->id === $request->user()->id && $validated['role'] !== 'administrator') {
            throw ValidationException::withMessages([
                'role' => 'You cannot remove your own administrator access.',
            ]);
        }

        DB::transaction(function () use ($validated, $user): void {
            $user->update([
                'name' => $validated['first_name'].' '.$validated['last_name'],
                'email' => $validated['email'],
            ]);
            $user->syncRoles([$validated['role']]);

            if ($validated['role'] === 'lecturer') {
                $lecturerData = [
                    'staff_number' => $validated['staff_number'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'user_id' => $user->id,
                ];

                if ($user->lecturer) {
                    $user->lecturer->update($lecturerData);
                } else {
                    Lecturer::create($lecturerData);
                }

                $user->student?->update(['user_id' => null]);
            } elseif ($validated['role'] === 'student') {
                $student = Student::findOrFail($validated['student_id']);
                if ($user->student && $user->student->id !== $student->id) {
                    $user->student->update(['user_id' => null]);
                }
                $student->update([
                    'user_id' => $user->id,
                    'email' => $validated['email'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                ]);
                $user->lecturer?->update(['user_id' => null]);
            } else {
                $user->lecturer?->update(['user_id' => null]);
                $user->student?->update(['user_id' => null]);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('status', 'User account updated.');
    }

    public function store(Request $request): RedirectResponse
    {
        $selectedStudent = $request->input('role') === 'student'
            ? Student::query()->find($request->integer('student_id'))
            : null;
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['administrator', 'lecturer', 'student'])],
            'staff_number' => ['required_if:role,lecturer', 'nullable', 'string', 'max:50', 'unique:lecturers,staff_number'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'student_id' => ['required_if:role,student', 'nullable', 'integer', Rule::exists('students', 'id'), Rule::unique('students', 'user_id')],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ];

        if ($request->input('role') === 'lecturer') {
            $rules['email'][] = Rule::unique('lecturers', 'email');
        }
        if ($request->input('role') === 'student') {
            $rules['email'][] = Rule::unique('students', 'email')->ignore($selectedStudent?->id);
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
            } elseif ($validated['role'] === 'student') {
                Student::query()->findOrFail($validated['student_id'])->update([
                    'user_id' => $user->id,
                    'email' => $validated['email'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                ]);
            }

            return $user;
        });

        return redirect()->route('admin.users.index')
            ->with('status', ucfirst($validated['role']).' account created for '.$user->name.'.');
    }
}
