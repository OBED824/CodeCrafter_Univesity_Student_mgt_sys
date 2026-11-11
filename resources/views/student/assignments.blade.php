@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<h1 class="text-2xl font-bold mb-2">My Assignments</h1>
<p class="text-slate-500 mb-6">Assignments posted for your registered courses.</p>

<div class="space-y-3">
    @forelse ($assignments as $assignment)
        <article class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-blue-700 font-medium">{{ $assignment->course->course_code }} — {{ $assignment->course->course_name }}</p>
                    <h2 class="text-lg font-semibold mt-1">{{ $assignment->title }}</h2>
                </div>
                <p class="text-sm text-slate-500 whitespace-nowrap">
                    Due: {{ $assignment->due_at?->format('d M Y, g:i A') ?? 'No due date' }}
                </p>
            </div>
            @if ($assignment->description)
                <p class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $assignment->description }}</p>
            @endif
            <p class="text-xs text-slate-400 mt-3">Lecturer: {{ $assignment->course->lecturer?->full_name ?? 'Unassigned' }}</p>
        </article>
    @empty
        <p class="text-slate-500">There are no assignments for your registered courses yet.</p>
    @endforelse
</div>
@endsection
