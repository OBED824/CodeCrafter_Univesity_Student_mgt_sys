@extends('layouts.app')

@section('title', 'Add Staff Account')

@section('content')
<h1 class="text-2xl font-bold mb-2">Add User Account</h1>
<p class="text-sm text-slate-500 mb-6">Create administrator, lecturer, or student logins. Create a student profile first before provisioning a student login.</p>

<form method="POST" action="{{ route('admin.users.store') }}" class="card p-6 space-y-4">
    @csrf
    <div>
        <label for="role" class="block text-sm font-medium mb-1">Role *</label>
        <select id="role" name="role" class="input-field" required>
            <option value="lecturer" @selected(old('role', 'lecturer') === 'lecturer')>Lecturer</option>
            <option value="administrator" @selected(old('role') === 'administrator')>Administrator</option>
            <option value="student" @selected(old('role') === 'student')>Student</option>
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium mb-1">First name *</label>
            <input id="first_name" name="first_name" value="{{ old('first_name') }}" class="input-field" maxlength="100" required>
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium mb-1">Last name *</label>
            <input id="last_name" name="last_name" value="{{ old('last_name') }}" class="input-field" maxlength="100" required>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email *</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="input-field" maxlength="255" required>
    </div>

    <div id="lecturer-fields" class="space-y-4">
        <div>
            <label for="staff_number" class="block text-sm font-medium mb-1">Staff number *</label>
            <input id="staff_number" name="staff_number" value="{{ old('staff_number') }}" class="input-field" maxlength="50">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="department" class="block text-sm font-medium mb-1">Department</label>
                <input id="department" name="department" value="{{ old('department') }}" class="input-field" maxlength="255">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" class="input-field" maxlength="20">
            </div>
        </div>
    </div>

    <div id="student-fields">
        <label for="student_id" class="block text-sm font-medium mb-1">Student profile *</label>
        <select id="student_id" name="student_id" class="input-field">
            <option value="">Select an unlinked student profile</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->full_name }} — {{ $student->student_number }}</option>
            @endforeach
        </select>
        @error('student_id')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="password" class="block text-sm font-medium mb-1">Temporary password *</label>
            <input id="password" type="password" name="password" class="input-field" minlength="8" required>
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm password *</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="input-field" minlength="8" required>
        </div>
    </div>

    <div class="pt-2 flex gap-2">
        <button type="submit" class="btn-primary">Create account</button>
        <a href="{{ route('admin.users.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>

<script>
    const roleField = document.getElementById('role');
    const lecturerFields = document.getElementById('lecturer-fields');
    const staffNumberField = document.getElementById('staff_number');
    const studentFields = document.getElementById('student-fields');
    const studentField = document.getElementById('student_id');

    function updateRoleFields() {
        const isLecturer = roleField.value === 'lecturer';
        const isStudent = roleField.value === 'student';
        lecturerFields.hidden = !isLecturer;
        staffNumberField.required = isLecturer;
        studentFields.hidden = !isStudent;
        studentField.required = isStudent;
    }

    roleField.addEventListener('change', updateRoleFields);
    updateRoleFields();
</script>
@endsection
