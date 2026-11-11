@extends('layouts.app')

@section('title', 'Edit User Access')

@section('content')
<h1 class="text-2xl font-bold mb-2">Edit User Access</h1>
<p class="text-slate-500 mb-6">{{ $user->name }} · {{ $user->email }}</p>

<form method="POST" action="{{ route('admin.users.update', $user) }}" class="card p-6 space-y-4">
    @csrf
    @method('PATCH')
    <div>
        <label for="role" class="block text-sm font-medium mb-1">Role *</label>
        <select id="role" name="role" class="input-field" required>
            @foreach (['administrator', 'lecturer', 'student'] as $role)
                <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name) === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        @error('role')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium mb-1">First name *</label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $user->student?->first_name ?? $user->lecturer?->first_name ?? str($user->name)->before(' ')) }}" class="input-field" maxlength="100" required>
            @error('first_name')<p class="error-text">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium mb-1">Last name *</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $user->student?->last_name ?? $user->lecturer?->last_name ?? str($user->name)->after(' ')) }}" class="input-field" maxlength="100" required>
            @error('last_name')<p class="error-text">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium mb-1">Email *</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" maxlength="255" required>
        @error('email')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    <div id="lecturer-fields" class="space-y-4">
        <div>
            <label for="staff_number" class="block text-sm font-medium mb-1">Staff number *</label>
            <input id="staff_number" name="staff_number" value="{{ old('staff_number', $user->lecturer?->staff_number) }}" class="input-field" maxlength="50">
            @error('staff_number')<p class="error-text">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="department" class="block text-sm font-medium mb-1">Department</label>
                <input id="department" name="department" value="{{ old('department', $user->lecturer?->department) }}" class="input-field" maxlength="255">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->lecturer?->phone) }}" class="input-field" maxlength="20">
            </div>
        </div>
    </div>

    <div id="student-fields">
        <label for="student_id" class="block text-sm font-medium mb-1">Student profile *</label>
        <select id="student_id" name="student_id" class="input-field">
            <option value="">Select a student profile</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id', $user->student?->id) == $student->id)>{{ $student->full_name }} — {{ $student->student_number }}</option>
            @endforeach
        </select>
        @error('student_id')<p class="error-text">{{ $message }}</p>@enderror
    </div>

    @if (auth()->id() === $user->id)
        <p class="text-sm text-amber-700">Your own role must remain administrator.</p>
    @endif

    <div class="pt-2 flex gap-2">
        <button type="submit" class="btn-primary">Save account changes</button>
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
