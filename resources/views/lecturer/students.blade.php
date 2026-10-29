@extends('layouts.app')

@section('title', $course->course_code.' Students')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $course->course_code }} — {{ $course->course_name }}</h1>
        <p class="text-slate-500 text-sm mt-1">Students registered for this course ({{ $students->count() }})</p>
    </div>
    <a href="{{ route('lecturer.courses') }}" class="btn-outline">Back to my courses</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr><th class="px-4 py-2 font-medium">Name</th><th class="px-4 py-2 font-medium">Student No.</th><th class="px-4 py-2 font-medium">Program</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($students as $student)
                <tr>
                    <td class="px-4 py-2">{{ $student->full_name }}</td>
                    <td class="px-4 py-2">{{ $student->student_number }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $student->program }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-4 text-slate-500">No students registered for this course yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
