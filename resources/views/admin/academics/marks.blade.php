@extends('layouts.app')

@section('title', 'Gradebook')

@section('content')
<h1 class="text-2xl font-bold mb-2">Central Gradebook</h1>
<p class="text-slate-500 mb-6">Review grades and correct mark entry errors across all courses.</p>

<form method="GET" action="{{ route('admin.marks.index') }}" class="card p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
    <div>
        <label for="course_id" class="block text-sm font-medium mb-1">Course</label>
        <select id="course_id" name="course_id" class="input-field">
            <option value="">All courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected(($filters['course_id'] ?? '') == $course->id)>{{ $course->course_code }} — {{ $course->course_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="student_id" class="block text-sm font-medium mb-1">Student</label>
        <select id="student_id" name="student_id" class="input-field">
            <option value="">All students</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected(($filters['student_id'] ?? '') == $student->id)>{{ $student->full_name }} ({{ $student->student_number }})</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('admin.marks.index') }}" class="btn-outline">Clear</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Student</th>
                    <th class="px-4 py-3 font-medium">Course</th>
                    <th class="px-4 py-3 font-medium">Assessment</th>
                    <th class="px-4 py-3 font-medium">Score / maximum</th>
                    <th class="px-4 py-3 font-medium">Update</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($marks as $mark)
                    <tr>
                        <td class="px-4 py-3">{{ $mark->student->full_name }} <span class="text-slate-500">({{ $mark->student->student_number }})</span></td>
                        <td class="px-4 py-3">{{ $mark->course->course_code }}</td>
                        <td class="px-4 py-3">{{ $mark->assessment_name }}</td>
                        <td class="px-4 py-3">{{ $mark->score }} / {{ $mark->maximum_score }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.marks.update', $mark) }}" class="grid min-w-72 grid-cols-3 gap-2">
                                @csrf
                                @method('PATCH')
                                <input name="assessment_name" value="{{ $mark->assessment_name }}" required maxlength="255" aria-label="Assessment name" class="input-field">
                                <input name="score" type="number" min="0" max="999999.99" step="0.01" value="{{ $mark->score }}" required aria-label="Score" class="input-field">
                                <div class="flex gap-2">
                                    <input name="maximum_score" type="number" min="0.01" max="999999.99" step="0.01" value="{{ $mark->maximum_score }}" required aria-label="Maximum score" class="input-field">
                                    <button type="submit" class="btn-outline">Save</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-5 text-slate-500">No marks match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $marks->links() }}</div>
@endsection
