<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Mark;
use App\Models\Note;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAcademicController extends Controller
{
    public function assignments(Request $request): View
    {
        $student = $this->currentStudent($request);
        $courseIds = $student->registrations()
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('course_id');

        return view('student.assignments', [
            'assignments' => Assignment::with('course.lecturer')
                ->whereIn('course_id', $courseIds)
                ->orderBy('due_at')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function marks(Request $request): View
    {
        $student = $this->currentStudent($request);

        return view('student.marks', [
            'marks' => Mark::with('course')
                ->whereBelongsTo($student)
                ->latest()
                ->get(),
        ]);
    }

    public function notes(Request $request): View
    {
        $student = $this->currentStudent($request);
        $courseIds = $student->registrations()
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('course_id');

        return view('student.notes', [
            'notes' => Note::with('course')
                ->whereIn('course_id', $courseIds)
                ->latest()
                ->get(),
        ]);
    }

    private function currentStudent(Request $request): Student
    {
        $student = $request->user()->student;

        abort_if($student === null, 403, 'Your account is not linked to a student record. Contact the administrator.');

        return $student;
    }
}
