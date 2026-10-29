@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-2">Welcome, {{ $student?->full_name ?? auth()->user()->name }}</h1>
@if ($student)
    <p class="text-slate-500 mb-6">{{ $student->student_number }} — {{ $student->program }}</p>
@else
    <p class="text-amber-600 mb-6">Your account is not linked to a student record yet. Contact the administrator.</p>
@endif

<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">My course registrations</h2>
    <a href="{{ route('student.courses') }}" class="btn-primary">Register for a course</a>
</div>

<div class="space-y-3">
    @forelse ($registrations as $registration)
        <div class="card p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $registration->course->course_code }} — {{ $registration->course->course_name }}</p>
                <p class="text-sm text-slate-500">Registered {{ $registration->registered_at?->format('d M Y') }}</p>
            </div>
            <span class="text-sm rounded-full px-3 py-1 bg-emerald-50 text-emerald-700">{{ ucfirst($registration->status) }}</span>
        </div>
    @empty
        <p class="text-slate-500">You have not registered for any courses yet.</p>
    @endforelse
</div>
@endsection
