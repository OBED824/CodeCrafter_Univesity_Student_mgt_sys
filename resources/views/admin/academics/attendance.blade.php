@extends('layouts.app')

@section('title', 'Attendance Log')

@section('content')
<h1 class="text-2xl font-bold mb-2">Attendance Log</h1>
<p class="text-slate-500 mb-6">Review and correct attendance records across the institution.</p>

<form method="GET" action="{{ route('admin.attendance.index') }}" class="card p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
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
    <div>
        <label for="date" class="block text-sm font-medium mb-1">Date</label>
        <input id="date" name="date" type="date" value="{{ $filters['date'] ?? '' }}" class="input-field">
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('admin.attendance.index') }}" class="btn-outline">Clear</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Student</th>
                    <th class="px-4 py-3 font-medium">Course</th>
                    <th class="px-4 py-3 font-medium">Recorded at</th>
                    <th class="px-4 py-3 font-medium">Correct timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($attendances as $attendance)
                    <tr>
                        <td class="px-4 py-3">{{ $attendance->student->full_name }} <span class="text-slate-500">({{ $attendance->student->student_number }})</span></td>
                        <td class="px-4 py-3">{{ $attendance->course->course_code }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $attendance->recorded_at->format('d M Y, g:i A') }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="flex min-w-64 gap-2">
                                @csrf
                                @method('PATCH')
                                <input name="recorded_at" type="datetime-local" value="{{ $attendance->recorded_at->format('Y-m-d\TH:i') }}" required class="input-field">
                                <button type="submit" class="btn-outline">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-5 text-slate-500">No attendance records match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $attendances->links() }}</div>
@endsection
