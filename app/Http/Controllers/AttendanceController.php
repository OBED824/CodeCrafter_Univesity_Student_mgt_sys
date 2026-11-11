<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->currentStudent($request);

        return view('student.attendance', [
            'student' => $student,
            'courses' => $student->courses()
                ->wherePivotIn('status', ['pending', 'approved'])
                ->with('lecturer')
                ->orderBy('course_code')
                ->get(),
            'attendances' => Attendance::with('course')
                ->whereBelongsTo($student)
                ->latest('recorded_at')
                ->paginate(20),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $student = $this->currentStudent($request);

        abort_unless($student->status === 'active', 403, 'Only active students can record attendance.');

        abort_unless(
            $student->registrations()
                ->where('course_id', $course->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists(),
            404
        );

        Attendance::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'recorded_at' => now(),
        ]);

        return redirect()->route('student.attendance.index')
            ->with('status', 'Attendance recorded for '.$course->course_code.'.');
    }

    private function currentStudent(Request $request): Student
    {
        $student = $request->user()->student;

        abort_if($student === null, 403, 'Your account is not linked to a student record. Contact the administrator.');

        return $student;
    }
}
