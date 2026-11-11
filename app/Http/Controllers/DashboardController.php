<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

/**
 * One dashboard per role. Each method only queries the data that role
 * is allowed to see — students never see other students' records and
 * lecturers only see courses assigned to them.
 */
class DashboardController extends Controller
{
    /**
     * Administrator: system-wide counts.
     */
    public function admin(): View
    {
        return view('admin.dashboard', [
            'studentCount' => Student::count(),
            'courseCount' => Course::count(),
            'registrationCount' => CourseRegistration::count(),
            'userCount' => User::count(),
            'attendanceCount' => Attendance::whereDate('recorded_at', today())->count(),
            'markCount' => Mark::count(),
            'activeStudentCount' => Student::where('status', 'active')->count(),
            'recentStudents' => Student::latest()->take(5)->get(),
        ]);
    }

    /**
     * Lecturer: their assigned courses and how many students each has.
     */
    public function lecturer(): View
    {
        $lecturer = auth()->user()->lecturer;

        return view('lecturer.dashboard', [
            'lecturer' => $lecturer,
            'courses' => $lecturer?->courses()->withCount('registeredStudents')->get() ?? collect(),
        ]);
    }

    /**
     * Student: their own registrations and program information.
     */
    public function student(): View
    {
        $student = auth()->user()->student;

        return view('student.dashboard', [
            'student' => $student,
            'registrations' => $student
                ? CourseRegistration::with('course')->where('student_id', $student->id)->get()
                : collect(),
        ]);
    }
}
