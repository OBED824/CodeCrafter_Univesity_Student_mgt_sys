@extends('layouts.main')

@section('content')

@include('layouts.header')

<div class="p-6 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-2">Welcome, {{ $lecturer?->full_name ?? auth()->user()->name }}</h1>
    <p class="text-slate-500 mb-6">Courses assigned to you and how many students are registered for each.</p>

    <div class="space-y-3">
        @forelse ($courses as $course)
            <div class="rounded-xl border border-slate-200 p-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">{{ $course->course_code }} — {{ $course->course_name }}</p>
                    <p class="text-sm text-slate-500">{{ $course->description }}</p>
                </div>
                <span class="text-sm bg-slate-100 rounded-full px-3 py-1">{{ $course->students_count }} student(s)</span>
            </div>
        @empty
            <p class="text-slate-500">You have no assigned courses yet.</p>
        @endforelse
    </div>
</div>

@include('layouts.footer')

@endsection
