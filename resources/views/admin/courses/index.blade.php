@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Courses</h1>
    <a href="{{ route('admin.courses.create') }}" class="btn-primary">+ Add course</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-2 font-medium">Code</th>
                <th class="px-4 py-2 font-medium">Course</th>
                <th class="px-4 py-2 font-medium">Lecturer</th>
                <th class="px-4 py-2 font-medium">Students</th>
                <th class="px-4 py-2 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($courses as $course)
                <tr>
                    <td class="px-4 py-2 font-medium">{{ $course->course_code }}</td>
                    <td class="px-4 py-2">{{ $course->course_name }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $course->lecturer?->full_name ?? 'Unassigned' }}</td>
                    <td class="px-4 py-2">{{ $course->students_count }}</td>
                    <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn-outline">View</a>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn-outline">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="inline" onsubmit="return confirm('Delete {{ $course->course_code }}? All its registrations will also be removed.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
