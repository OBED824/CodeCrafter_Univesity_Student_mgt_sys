@extends('layouts.app')

@section('title', 'Course Notes')

@section('content')
<h1 class="text-2xl font-bold mb-2">Course Notes</h1>
<p class="text-slate-500 mb-6">Notes posted by lecturers for your registered courses.</p>

<div class="space-y-3">
    @forelse ($notes as $note)
        <article class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                <div>
                    <p class="text-sm text-blue-700 font-medium">{{ $note->course->course_code }} — {{ $note->course->course_name }}</p>
                    <h2 class="text-lg font-semibold mt-1">{{ $note->title }}</h2>
                </div>
                <p class="text-sm text-slate-500 whitespace-nowrap">{{ $note->created_at->format('d M Y, g:i A') }}</p>
            </div>
            <div class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $note->content }}</div>
        </article>
    @empty
        <p class="text-slate-500">There are no notes for your registered courses yet.</p>
    @endforelse
</div>
@endsection
