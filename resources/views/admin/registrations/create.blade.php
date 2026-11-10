@extends('layouts.app')

@section('title', 'Add Course Registration')

@section('content')
<h1 class="text-2xl font-bold mb-2">Add Course Registration</h1>
<p class="text-sm text-slate-500 mb-6">Register a student in a course and set the initial registration status.</p>

<form method="POST" action="{{ route('admin.registrations.store') }}" class="card p-6 space-y-4">
    @csrf
    <div>
        <label for="student_id" class="block text-sm font-medium mb-1">Student *</label>
        <select id="student_id" name="student_id" class="input-field" required>
            <option value="">Select a student</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                    {{ $student->full_name }} — {{ $student->student_number }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="course_id" class="block text-sm font-medium mb-1">Course *</label>
        <select id="course_id" name="course_id" class="input-field" required>
            <option value="">Select a course</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>
                    {{ $course->course_code }} — {{ $course->course_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium mb-1">Status *</label>
        <select id="status" name="status" class="input-field" required>
            @foreach (['pending', 'approved', 'withdrawn'] as $status)
                <option value="{{ $status }}" @selected(old('status', 'approved') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>

    <div class="pt-2 flex gap-2">
        <button type="submit" class="btn-primary">Create registration</button>
        <a href="{{ route('admin.registrations.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
