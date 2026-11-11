@extends('layouts.app')

@section('title', $course->course_code.' Notes')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $course->course_code }} Course Notes</h1>
        <p class="text-slate-500 mt-1">{{ $course->course_name }}</p>
    </div>
    <a href="{{ route('lecturer.courses') }}" class="btn-outline">Back to my courses</a>
</div>

<form method="POST" action="{{ route('lecturer.courses.notes.store', $course) }}" class="card p-5 space-y-4 mb-8">
    @csrf
    <h2 class="text-lg font-semibold">Post course notes</h2>
    <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="input-field">
        @error('title')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="content" class="block text-sm font-medium mb-1">Notes</label>
        <textarea id="content" name="content" rows="8" maxlength="50000" required class="input-field">{{ old('content') }}</textarea>
        @error('content')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div><button type="submit" class="btn-primary">Post notes</button></div>
</form>

<h2 class="text-lg font-semibold mb-3">Posted notes</h2>
<div class="space-y-3">
    @forelse ($notes as $note)
        <article class="card p-5">
            <h3 class="font-semibold">{{ $note->title }}</h3>
            <p class="text-xs text-slate-400 mt-1">Posted {{ $note->created_at->format('d M Y, g:i A') }}</p>
            <div class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $note->content }}</div>
        </article>
    @empty
        <p class="text-slate-500">No notes have been posted for this course yet.</p>
    @endforelse
</div>
@endsection
