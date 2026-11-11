<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Student management — administrator only (enforced by route middleware).
 * Supports list + search, create, show, edit, delete.
 */
class StudentController extends Controller
{
    /**
     * List students, newest first, with an optional name/number search.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $students = Student::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'search'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    /**
     * Validate then save a new student.
     * The 'unique:students' rules stop duplicate student numbers/emails
     * before they reach the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'program' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated): void {
            $student = Student::create([
                ...$validated,
                'status' => 'active',
            ]);
            $student->lifecycleEvents()->create([
                'event_type' => 'admitted',
                'new_program' => $student->program,
                'new_status' => $student->status,
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('status', 'Student created successfully.');
    }

    public function show(Student $student): View
    {
        $student->load([
            'courses.lecturer',
            'lifecycleEvents' => fn ($query) => $query->latest(),
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update an existing student. The unique rule must ignore the row
     * being edited, otherwise Laravel would flag the student's own
     * current number/email as a duplicate.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number,'.$student->id],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'program' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:active,transferred,graduated,withdrawn'],
            'lifecycle_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $previousProgram = $student->program;
        $previousStatus = $student->status;
        DB::transaction(function () use ($validated, $student, $previousProgram, $previousStatus): void {
            $updateData = collect($validated)->except('lifecycle_notes')->all();
            $student->update($updateData);
            $student->user?->update([
                'name' => $student->full_name,
                'email' => $student->email,
            ]);

            if ($previousProgram !== $student->program || $previousStatus !== $student->status) {
                $eventType = $previousProgram !== $student->program
                    ? 'transferred'
                    : $student->status;

                $student->lifecycleEvents()->create([
                    'event_type' => $eventType,
                    'previous_program' => $previousProgram,
                    'new_program' => $student->program,
                    'previous_status' => $previousStatus,
                    'new_status' => $student->status,
                    'notes' => $validated['lifecycle_notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.students.show', $student)
            ->with('status', 'Student updated successfully.');
    }

    /**
     * Delete a student. Related registrations are removed automatically
     * by the cascadeOnDelete() set in the migration.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('status', 'Student deleted successfully.');
    }
}
