<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LecturerAcademicController extends Controller
{
    public function assignments(Course $course): View
    {
        $this->authorizeCourse($course);

        return view('lecturer.assignments', [
            'course' => $course,
            'assignments' => $course->assignments()->latest()->get(),
        ]);
    }

    public function storeAssignment(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_at' => ['nullable', 'date'],
        ]);

        $course->assignments()->create($validated);

        return redirect()->route('lecturer.courses.assignments', $course)
            ->with('status', 'Assignment posted successfully.');
    }

    public function notes(Course $course): View
    {
        $this->authorizeCourse($course);

        return view('lecturer.notes', [
            'course' => $course,
            'notes' => $course->notes()->latest()->get(),
        ]);
    }

    public function storeNote(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
        ]);

        $course->notes()->create($validated);

        return redirect()->route('lecturer.courses.notes', $course)
            ->with('status', 'Course note posted successfully.');
    }

    public function marks(Course $course): View
    {
        $this->authorizeCourse($course);

        return view('lecturer.marks', [
            'course' => $course,
            'students' => $course->registeredStudents()->orderBy('last_name')->get(),
            'marks' => $course->marks()->with('student')->latest()->get(),
        ]);
    }

    public function storeMark(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'assessment_name' => ['required', 'string', 'max:255'],
            'score' => ['required', 'numeric', 'min:0', 'max:999999.99', 'lte:maximum_score'],
            'maximum_score' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
        ]);

        if (! $course->registeredStudents()->whereKey($validated['student_id'])->exists()) {
            throw ValidationException::withMessages([
                'student_id' => 'Choose a student registered for this course.',
            ]);
        }

        $course->marks()->updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'assessment_name' => $validated['assessment_name'],
            ],
            [
                'score' => $validated['score'],
                'maximum_score' => $validated['maximum_score'],
            ]
        );

        return redirect()->route('lecturer.courses.marks', $course)
            ->with('status', 'Mark saved successfully.');
    }

    private function authorizeCourse(Course $course): void
    {
        $lecturer = auth()->user()->lecturer;

        abort_if($lecturer === null || $course->lecturer_id !== $lecturer->id, 404);
    }
}
