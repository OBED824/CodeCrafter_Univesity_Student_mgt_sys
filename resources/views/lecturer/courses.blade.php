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
            <a href="{{ route('lecturer.courses.students', $course) }}" class="btn-outline whitespace-nowrap">
                {{ $course->students_count }} registered student(s) →
            </a>
        </div>
    @endforeach
</div>
@endsection
