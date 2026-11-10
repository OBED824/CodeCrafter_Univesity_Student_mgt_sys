<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = CourseRegistration::query()
            ->with(['student', 'course'])
            ->latest()
            ->paginate(15);

        return view('admin.registrations.index', compact('registrations'));
    }

    public function create(): View
    {
        return view('admin.registrations.create', [
            'students' => Student::orderBy('last_name')->orderBy('first_name')->get(),
            'courses' => Course::orderBy('course_code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('course_registrations', 'course_id')
                    ->where('student_id', $request->input('student_id')),
            ],
            'status' => ['required', Rule::in(['pending', 'approved', 'withdrawn'])],
        ]);

        CourseRegistration::create([
            ...$validated,
            'registered_at' => now(),
        ]);

        return redirect()->route('admin.registrations.index')
            ->with('status', 'Course registration created.');
    }

    public function update(Request $request, CourseRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'withdrawn'])],
        ]);

        $registration->update($validated);

        return redirect()->route('admin.registrations.index')
            ->with('status', 'Registration status updated.');
    }

    public function destroy(CourseRegistration $registration): RedirectResponse
    {
        $registration->delete();

        return redirect()->route('admin.registrations.index')
            ->with('status', 'Registration deleted.');
    }
}
