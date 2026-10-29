<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Course registration for the logged-in student.
 *
 * A student can only ever act on their own record: the student is looked
 * up from auth()->user()->student, never from an ID sent by the browser.
 * That is what stops one student registering or viewing another's records.
 */
class RegistrationController extends Controller
{
    /**
     * Available courses with a flag showing which ones this student
     * has already registered for.
     */
    public function available(Request $request): View
    {
        $student = $this->currentStudent();

        $registeredCourseIds = $student->courses()->pluck('courses.id');

        return view('student.courses', [
            'student' => $student,
            'courses' => Course::with('lecturer')->orderBy('course_code')->get(),
            'registeredCourseIds' => $registeredCourseIds,
        ]);
    }

    /**
     * The student's own registrations.
     */
    public function mine(): View
    {
        $student = $this->currentStudent();

        return view('student.registrations', [
            'student' => $student,
            'registrations' => $student->registrations()->with('course.lecturer')->latest()->get(),
        ]);
    }

    /**
     * Register for a course. Duplicate registration is blocked twice:
     * once here with a friendly message, and again by the database
     * unique index as a hard guarantee.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $student = $this->currentStudent();

        if ($student->courses()->whereKey($course->id)->exists()) {
            return back()->with('error', 'You are already registered for this course.');
        }

        $student->courses()->attach($course->id, [
            'status' => 'approved',
            'registered_at' => now(),
        ]);

        return redirect()->route('student.registrations.index')
            ->with('status', 'Course registered successfully.');
    }

    /**
     * Cancel (withdraw from) one of the student's own registrations.
     */
    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $student = $this->currentStudent();

        $student->courses()->detach($course->id);

        return redirect()->route('student.registrations.index')
            ->with('status', 'Registration cancelled.');
    }

    /**
     * Resolve the student record that belongs to the logged-in user,
     * failing clearly if their account is not linked to one yet.
     */
    private function currentStudent(): Student
    {
        $student = auth()->user()->student;

        abort_if($student === null, 403, 'Your account is not linked to a student record. Contact the administrator.');

        return $student;
    }
}
