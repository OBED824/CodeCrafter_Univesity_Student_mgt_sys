@extends('layouts.main')

@section('content')

@include('layouts.header')

<div class="p-6 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Administrator Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Students</p>
            <p class="text-2xl font-bold">{{ $studentCount }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Courses</p>
            <p class="text-2xl font-bold">{{ $courseCount }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Registrations</p>
            <p class="text-2xl font-bold">{{ $registrationCount }}</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Recently added students</h2>
    <ul class="list-disc pl-6 text-slate-700 space-y-1">
        @forelse ($recentStudents as $student)
            <li>{{ $student->full_name }} — {{ $student->student_number }} ({{ $student->program }})</li>
        @empty
            <li>No students yet.</li>
        @endforelse
    </ul>
</div>

@include('layouts.footer')

@endsection
