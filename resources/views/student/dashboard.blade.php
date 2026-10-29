@extends('layouts.main')

@section('content')

@include('layouts.header')

<div class="p-6 max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-2">Welcome, {{ $student?->full_name ?? auth()->user()->name }}</h1>
    @if ($student)
        <p class="text-slate-500 mb-6">{{ $student->student_number }} — {{ $student->program }}</p>
    @else
        <p class="text-amber-600 mb-6">Your account is not linked to a student record yet. Contact the administrator.</p>
    @endif

    <h2 class="text-lg font-semibold mb-3">My course registrations</h2>
    <div class="space-y-3">
        @forelse ($registrations as $registration)
            <div class="rounded-xl border border-slate-200 p-4 flex justify-between items-center">
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
</div>

@include('layouts.footer')

@endsection
