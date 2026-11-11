@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">{{ $student->full_name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn-outline">Edit</a>
        <a href="{{ route('admin.students.index') }}" class="btn-outline">Back to list</a>
    </div>
</div>

<div class="card p-6 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
    <div><p class="text-slate-500">Student number</p><p class="font-medium">{{ $student->student_number }}</p></div>
    <div><p class="text-slate-500">Program</p><p class="font-medium">{{ $student->program }}</p></div>
    <div><p class="text-slate-500">Status</p><p class="font-medium capitalize">{{ $student->status }}</p></div>
    <div><p class="text-slate-500">Email</p><p class="font-medium">{{ $student->email }}</p></div>
    <div><p class="text-slate-500">Phone</p><p class="font-medium">{{ $student->phone ?? '—' }}</p></div>
    <div><p class="text-slate-500">Gender</p><p class="font-medium">{{ ucfirst($student->gender) }}</p></div>
    <div><p class="text-slate-500">Date of birth</p><p class="font-medium">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</p></div>
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">Student lifecycle history</h2>
    <a href="{{ route('admin.students.edit', $student) }}" class="btn-outline">Update status or program</a>
</div>
<div class="space-y-3 mb-8">
    @forelse ($student->lifecycleEvents as $event)
        <article class="card p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <p class="font-medium capitalize">{{ str_replace('_', ' ', $event->event_type) }}</p>
                <p class="text-xs text-slate-500">{{ $event->created_at->format('d M Y, g:i A') }}</p>
            </div>
            @if ($event->previous_program !== $event->new_program && ($event->previous_program || $event->new_program))
                <p class="text-sm text-slate-600 mt-1">{{ $event->previous_program ?? '—' }} → {{ $event->new_program ?? '—' }}</p>
            @endif
            @if ($event->previous_status !== $event->new_status && ($event->previous_status || $event->new_status))
                <p class="text-sm text-slate-600 mt-1">{{ ucfirst($event->previous_status ?? '—') }} → {{ ucfirst($event->new_status ?? '—') }}</p>
            @endif
            @if ($event->notes)
                <p class="text-sm text-slate-500 mt-1">{{ $event->notes }}</p>
            @endif
        </article>
    @empty
        <p class="text-slate-500">No lifecycle changes have been recorded.</p>
    @endforelse
</div>

<h2 class="text-lg font-semibold mb-3">Registered courses</h2>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr><th class="px-4 py-2 font-medium">Code</th><th class="px-4 py-2 font-medium">Course</th><th class="px-4 py-2 font-medium">Lecturer</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($student->courses as $course)
                <tr>
                    <td class="px-4 py-2">{{ $course->course_code }}</td>
                    <td class="px-4 py-2">{{ $course->course_name }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $course->lecturer?->full_name ?? 'Unassigned' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-4 text-slate-500">Not registered for any course yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
