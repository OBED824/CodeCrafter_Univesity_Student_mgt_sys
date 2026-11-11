@extends('layouts.app')

@section('title', $course->course_code.' Assignments')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $course->course_code }} Assignments</h1>
        <p class="text-slate-500 mt-1">{{ $course->course_name }}</p>
    </div>
    <a href="{{ route('lecturer.courses') }}" class="btn-outline">Back to my courses</a>
</div>

<form method="POST" action="{{ route('lecturer.courses.assignments.store', $course) }}" class="card p-5 space-y-4 mb-8">
    @csrf
    <h2 class="text-lg font-semibold">Post an assignment</h2>
    <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="input-field">
        @error('title')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="description" class="block text-sm font-medium mb-1">Instructions</label>
        <textarea id="description" name="description" rows="4" maxlength="5000" class="input-field">{{ old('description') }}</textarea>
        @error('description')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="due_at" class="block text-sm font-medium mb-1">Due date (optional)</label>
        <input id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at') }}" class="input-field">
        @error('due_at')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div><button type="submit" class="btn-primary">Post assignment</button></div>
</form>

<h2 class="text-lg font-semibold mb-3">Posted assignments</h2>
<div class="space-y-3">
    @forelse ($assignments as $assignment)
        <article class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                <h3 class="font-semibold">{{ $assignment->title }}</h3>
                <p class="text-sm text-slate-500">Due: {{ $assignment->due_at?->format('d M Y, g:i A') ?? 'No due date' }}</p>
            </div>
            @if ($assignment->description)
                <p class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $assignment->description }}</p>
            @endif
        </article>
    @empty
        <p class="text-slate-500">No assignments have been posted for this course.</p>
    @endforelse
</div>
@endsection
