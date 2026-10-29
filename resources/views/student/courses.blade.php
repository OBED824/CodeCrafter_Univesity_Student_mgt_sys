@extends('layouts.app')

@section('title', 'Available Courses')

@section('content')
<h1 class="text-2xl font-bold mb-2">Available Courses</h1>
<p class="text-slate-500 mb-6">Register for a course with one click. Already-registered courses are marked.</p>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    @foreach ($courses as $course)
        @php($isRegistered = $registeredCourseIds->contains($course->id))
        <div class="card p-5">
            <div class="flex justify-between items-start mb-2">
                <p class="font-semibold">{{ $course->course_code }}</p>
                @if ($isRegistered)
                    <span class="text-xs bg-emerald-50 text-emerald-700 rounded-full px-2.5 py-1">Registered</span>
                @endif
            </div>
            <p class="font-medium mb-1">{{ $course->course_name }}</p>
            <p class="text-sm text-slate-500 mb-4">{{ $course->description }}</p>
            <p class="text-xs text-slate-400 mb-4">Lecturer: {{ $course->lecturer?->full_name ?? 'Unassigned' }}</p>

            @if ($isRegistered)
                <form method="POST" action="{{ route('student.registrations.destroy', $course) }}" onsubmit="return confirm('Cancel your registration for {{ $course->course_code }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline">Cancel registration</button>
                </form>
            @else
                <form method="POST" action="{{ route('student.registrations.store', $course) }}">
                    @csrf
                    <button type="submit" class="btn-primary">Register for this course</button>
                </form>
            @endif
        </div>
    @endforeach
</div>
@endsection
