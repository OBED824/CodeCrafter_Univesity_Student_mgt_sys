@extends('layouts.app')

@section('title', 'My Registrations')

@section('content')
<h1 class="text-2xl font-bold mb-6">My Course Registrations</h1>

<div class="space-y-3">
    @forelse ($registrations as $registration)
        <div class="card p-4 flex justify-between items-center gap-4">
            <div>
                <p class="font-semibold">{{ $registration->course->course_code }} — {{ $registration->course->course_name }}</p>
                <p class="text-sm text-slate-500">Lecturer: {{ $registration->course->lecturer?->full_name ?? 'Unassigned' }} · Registered {{ $registration->registered_at?->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm rounded-full px-3 py-1 bg-emerald-50 text-emerald-700">{{ ucfirst($registration->status) }}</span>
                <form method="POST" action="{{ route('student.registrations.destroy', $registration->course) }}" onsubmit="return confirm('Cancel registration for {{ $registration->course->course_code }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-outline">Cancel</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-slate-500">You have not registered for any courses yet.</p>
        <a href="{{ route('student.courses') }}" class="btn-primary mt-2">Browse available courses</a>
    @endforelse
</div>
@endsection
