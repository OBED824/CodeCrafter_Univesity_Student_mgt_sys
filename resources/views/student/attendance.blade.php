@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
<h1 class="text-2xl font-bold mb-2">Record Attendance</h1>
<p class="text-slate-500 mb-6">Select a registered course each time you attend a class. Every submission creates a timestamped attendance record.</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    @forelse ($courses as $course)
        <article class="card p-5">
            <p class="font-semibold">{{ $course->course_code }} — {{ $course->course_name }}</p>
            <p class="text-sm text-slate-500 mt-1">Lecturer: {{ $course->lecturer?->full_name ?? 'Unassigned' }}</p>
            @if ($student->status === 'active')
                <form method="POST" action="{{ route('student.attendance.store', $course) }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn-primary">Record attendance</button>
                </form>
            @else
                <p class="text-sm text-amber-700 mt-4">Only active students can record attendance.</p>
            @endif
        </article>
    @empty
        <p class="text-slate-500">Register for a course before recording attendance.</p>
    @endforelse
</div>

<h2 class="text-lg font-semibold mb-3">Attendance history</h2>
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Course</th>
                    <th class="px-4 py-3 font-medium">Recorded at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($attendances as $attendance)
                    <tr>
                        <td class="px-4 py-3">{{ $attendance->course->course_code }} — {{ $attendance->course->course_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $attendance->recorded_at->format('d M Y, g:i:s A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-5 text-slate-500">No attendance has been recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{ $attendances->links() }}
@endsection
