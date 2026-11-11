<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAcademicController extends Controller
{
    public function attendance(Request $request): View
    {
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'date' => ['nullable', 'date'],
        ]);

        $attendances = Attendance::query()
            ->with(['student', 'course'])
            ->when($filters['course_id'] ?? null, fn ($query, $courseId) => $query->where('course_id', $courseId))
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->whereDate('recorded_at', $date))
            ->latest('recorded_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.academics.attendance', [
            'attendances' => $attendances,
            'courses' => Course::orderBy('course_code')->get(),
            'students' => Student::orderBy('last_name')->orderBy('first_name')->get(),
            'filters' => $filters,
        ]);
    }

    public function updateAttendance(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'recorded_at' => ['required', 'date'],
        ]);

        $attendance->update($validated);

        return back()->with('status', 'Attendance record updated.');
    }

    public function marks(Request $request): View
    {
        $filters = $request->validate([
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
        ]);

        return view('admin.academics.marks', [
            'marks' => Mark::query()
                ->with(['student', 'course'])
                ->when($filters['course_id'] ?? null, fn ($query, $courseId) => $query->where('course_id', $courseId))
                ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'courses' => Course::orderBy('course_code')->get(),
            'students' => Student::orderBy('last_name')->orderBy('first_name')->get(),
            'filters' => $filters,
        ]);
    }

    public function updateMark(Request $request, Mark $mark): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_name' => ['required', 'string', 'max:255'],
            'score' => ['required', 'numeric', 'min:0', 'max:999999.99', 'lte:maximum_score'],
            'maximum_score' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
        ]);

        $duplicateExists = Mark::query()
            ->where('course_id', $mark->course_id)
            ->where('student_id', $mark->student_id)
            ->where('assessment_name', $validated['assessment_name'])
            ->whereKeyNot($mark->id)
            ->exists();

        if ($duplicateExists) {
            throw ValidationException::withMessages([
                'assessment_name' => 'This student already has a mark for this assessment.',
            ]);
        }

        $mark->update($validated);

        return back()->with('status', 'Mark updated.');
    }
}
