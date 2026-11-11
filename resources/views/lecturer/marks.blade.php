@extends('layouts.app')

@section('title', $course->course_code.' Marks')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $course->course_code }} Marks</h1>
        <p class="text-slate-500 mt-1">{{ $course->course_name }}</p>
    </div>
    <a href="{{ route('lecturer.courses') }}" class="btn-outline">Back to my courses</a>
</div>

<form method="POST" action="{{ route('lecturer.courses.marks.store', $course) }}" class="card p-5 space-y-4 mb-8">
    @csrf
    <h2 class="text-lg font-semibold">Record a mark</h2>
    <div>
        <label for="student_id" class="block text-sm font-medium mb-1">Student</label>
        <select id="student_id" name="student_id" required class="input-field">
            <option value="">Select a registered student</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->full_name }} — {{ $student->student_number }}</option>
            @endforeach
        </select>
        @error('student_id')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="assessment_name" class="block text-sm font-medium mb-1">Assessment</label>
        <input id="assessment_name" name="assessment_name" value="{{ old('assessment_name') }}" required maxlength="255" class="input-field">
        @error('assessment_name')<p class="error-text">{{ $message }}</p>@enderror
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="score" class="block text-sm font-medium mb-1">Score</label>
            <input id="score" name="score" type="number" min="0" step="0.01" value="{{ old('score') }}" required class="input-field">
            @error('score')<p class="error-text">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="maximum_score" class="block text-sm font-medium mb-1">Maximum score</label>
            <input id="maximum_score" name="maximum_score" type="number" min="0.01" step="0.01" value="{{ old('maximum_score') }}" required class="input-field">
            @error('maximum_score')<p class="error-text">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <button type="submit" class="btn-primary" @disabled($students->isEmpty())>Save mark</button>
        @if ($students->isEmpty())
            <p class="text-sm text-slate-500 mt-2">There are no active registered students in this course.</p>
        @endif
    </div>
</form>

<h2 class="text-lg font-semibold mb-3">Recorded marks</h2>
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Student</th>
                    <th class="px-4 py-3 font-medium">Assessment</th>
                    <th class="px-4 py-3 font-medium">Mark</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($marks as $mark)
                    <tr>
                        <td class="px-4 py-3">{{ $mark->student->full_name }}</td>
                        <td class="px-4 py-3">{{ $mark->assessment_name }}</td>
                        <td class="px-4 py-3">{{ $mark->score }} / {{ $mark->maximum_score }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-5 text-slate-500">No marks have been recorded for this course yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
