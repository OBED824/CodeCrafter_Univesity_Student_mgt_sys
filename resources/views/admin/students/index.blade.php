@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Students</h1>
    <a href="{{ route('admin.students.create') }}" class="btn-primary">+ Add student</a>
</div>

<form method="GET" action="{{ route('admin.students.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, number or email" class="input-field max-w-sm">
    <button type="submit" class="btn-outline">Search</button>
    @if ($search)
        <a href="{{ route('admin.students.index') }}" class="btn-outline">Clear</a>
    @endif
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-2 font-medium">Name</th>
                <th class="px-4 py-2 font-medium">Student No.</th>
                <th class="px-4 py-2 font-medium">Program</th>
                <th class="px-4 py-2 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($students as $student)
                <tr>
                    <td class="px-4 py-2">{{ $student->full_name }}</td>
                    <td class="px-4 py-2">{{ $student->student_number }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $student->program }}</td>
                    <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.students.show', $student) }}" class="btn-outline">View</a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn-outline">Edit</a>
                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline" onsubmit="return confirm('Delete {{ $student->full_name }}? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">No students found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $students->links() }}
</div>
@endsection
