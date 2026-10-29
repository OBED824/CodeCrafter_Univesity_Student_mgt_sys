<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Course management — administrator has full CRUD.
 */
class CourseController extends Controller
{
    public function index(): View
    {
        return view('admin.courses.index', [
            'courses' => Course::with('lecturer')->withCount('students')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.create', ['lecturers' => Lecturer::orderBy('first_name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'lecturer_id' => ['nullable', 'exists:lecturers,id'],
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('status', 'Course created successfully.');
    }

    public function show(Course $course): View
    {
        $course->load('lecturer', 'students');

        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.edit', [
            'course' => $course,
            'lecturers' => Lecturer::orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code,'.$course->id],
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'lecturer_id' => ['nullable', 'exists:lecturers,id'],
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('status', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('status', 'Course deleted successfully.');
    }
}
