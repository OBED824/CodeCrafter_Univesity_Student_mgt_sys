@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<h1 class="text-2xl font-bold mb-6">My Courses</h1>

@unless ($courses->count())
    <p class="text-slate-500">You have no assigned courses yet.</p>
@endunless

<div class="space-y-3">
    @foreach ($courses as $course)
        <div class="card p-4 flex justify-between items-center gap-4">
            <div>
                <p class="font-semibold">{{ $course->course_code }} — {{ $course->course_name }}</p>
                <p class="text-sm text-slate-500">{{ $course->description }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('lecturer.courses.assignments', $course) }}" class="btn-outline whitespace-nowrap">Assignments</a>
                <a href="{{ route('lecturer.courses.notes', $course) }}" class="btn-outline whitespace-nowrap">Notes</a>
                <a href="{{ route('lecturer.courses.marks', $course) }}" class="btn-outline whitespace-nowrap">Marks</a>
                <a href="{{ route('lecturer.courses.students', $course) }}" class="btn-outline whitespace-nowrap">
                    {{ $course->registered_students_count }} student(s) →
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
