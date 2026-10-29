<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

/**
 * Read-only views for lecturers: their assigned courses and the
 * students registered in each.
 *
 * Authorization here satisfies "lecturers must not access records they
 * are not allowed to": every lookup is filtered by the lecturer record
 * linked to the logged-in account, so a lecturer can never open another
 * lecturer's course (they get a 404 instead).
 */
class LecturerController extends Controller
{
    public function courses(): View
    {
        $lecturer = auth()->user()->lecturer;

        return view('lecturer.courses', [
            'courses' => $lecturer?->courses()->withCount('students')->orderBy('course_code')->get() ?? collect(),
        ]);
    }

    public function students(Course $course): View
    {
        $lecturer = auth()->user()->lecturer;

        abort_if($lecturer === null || $course->lecturer_id !== $lecturer->id, 404);

        return view('lecturer.students', [
            'course' => $course->load('lecturer'),
            'students' => $course->students()->orderBy('last_name')->get(),
        ]);
    }
}
