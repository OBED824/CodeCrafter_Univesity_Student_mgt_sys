@extends('layouts.app')

@section('title', 'Institutional Reports')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Institutional Reports</h1>
        <p class="text-slate-500 mt-1">Attendance and grade summaries for a selected period.</p>
    </div>
    <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-outline">Export CSV</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach ($studentCounts as $status => $count)
        <div class="card p-4">
            <p class="text-sm text-slate-500 capitalize">{{ $status }} students</p>
            <p class="text-2xl font-bold">{{ $count }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    @foreach ($accountCounts as $role => $count)
        <div class="card p-4">
            <p class="text-sm text-slate-500 capitalize">{{ $role }} accounts</p>
            <p class="text-2xl font-bold">{{ $count }}</p>
        </div>
    @endforeach
</div>

<form method="GET" action="{{ route('admin.reports.index') }}" class="card p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
    <div>
        <label for="start_date" class="block text-sm font-medium mb-1">Start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] }}" required class="input-field">
    </div>
    <div>
        <label for="end_date" class="block text-sm font-medium mb-1">End date</label>
        <input id="end_date" name="end_date" type="date" value="{{ $filters['end_date'] }}" required class="input-field">
    </div>
    <div>
        <label for="course_id" class="block text-sm font-medium mb-1">Course</label>
        <select id="course_id" name="course_id" class="input-field">
            <option value="">All courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected(($filters['course_id'] ?? '') == $course->id)>{{ $course->course_code }} — {{ $course->course_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="btn-primary">Generate report</button>
        <a href="{{ route('admin.reports.index') }}" class="btn-outline">Reset</a>
    </div>
</form>

<h2 class="text-lg font-semibold mb-3">Attendance by course</h2>
<div class="card overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr><th class="px-4 py-3 font-medium">Course</th><th class="px-4 py-3 font-medium">Attendance records</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($attendanceSummary as $summary)
                    <tr>
                        <td class="px-4 py-3">{{ $summary->course->course_code }} — {{ $summary->course->course_name }}</td>
                        <td class="px-4 py-3">{{ $summary->attendance_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-4 py-5 text-slate-500">No attendance records in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h2 class="text-lg font-semibold mb-3">Marks by course</h2>
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr><th class="px-4 py-3 font-medium">Course</th><th class="px-4 py-3 font-medium">Assessment entries</th><th class="px-4 py-3 font-medium">Weighted average</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($marksSummary as $summary)
                    <tr>
                        <td class="px-4 py-3">{{ $summary->course->course_code }} — {{ $summary->course->course_name }}</td>
                        <td class="px-4 py-3">{{ $summary->assessment_count }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $summary->average_percentage, 2) }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-5 text-slate-500">No marks recorded in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
