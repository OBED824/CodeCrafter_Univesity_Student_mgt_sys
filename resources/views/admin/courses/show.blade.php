@extends('layouts.app')

@section('title', $course->course_code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">{{ $course->course_code }} — {{ $course->course_name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn-outline">Edit</a>
        <a href="{{ route('admin.courses.index') }}" class="btn-outline">Back to list</a>
    </div>
</div>

<div class="card p-6 mb-6 text-sm">
    <p class="text-slate-500">Lecturer</p>
    <p class="font-medium mb-3">{{ $course->lecturer?->full_name ?? 'Unassigned' }}</p>
    <p class="text-slate-500">Description</p>
    <p>{{ $course->description ?: 'No description provided.' }}</p>
</div>

<h2 class="text-lg font-semibold mb-3">Registered students ({{ $course->students->count() }})</h2>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr><th class="px-4 py-2 font-medium">Name</th><th class="px-4 py-2 font-medium">Student No.</th><th class="px-4 py-2 font-medium">Program</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($course->students as $student)
                <tr>
                    <td class="px-4 py-2"><a href="{{ route('admin.students.show', $student) }}" class="text-[#2563eb]">{{ $student->full_name }}</a></td>
                    <td class="px-4 py-2">{{ $student->student_number }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $student->program }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-4 text-slate-500">No students registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
