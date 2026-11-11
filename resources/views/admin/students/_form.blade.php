{{-- Shared student form fields. Expects $student (null when creating). --}}
@php($student = $student ?? null)

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Student number *</label>
        <input type="text" name="student_number" value="{{ old('student_number', $student?->student_number) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Program / course of study *</label>
        <input type="text" name="program" value="{{ old('program', $student?->program) }}" class="input-field" required>
    </div>
    @if ($student)
        <div>
            <label class="block text-sm font-medium mb-1">Student status *</label>
            <select name="status" class="input-field" required>
                @foreach (['active', 'transferred', 'graduated', 'withdrawn'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $student->status) === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Lifecycle note</label>
            <input type="text" name="lifecycle_notes" value="{{ old('lifecycle_notes') }}" maxlength="2000" class="input-field" placeholder="Optional reason or reference for this change">
        </div>
    @endif
    <div>
        <label class="block text-sm font-medium mb-1">First name *</label>
        <input type="text" name="first_name" value="{{ old('first_name', $student?->first_name) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Last name *</label>
        <input type="text" name="last_name" value="{{ old('last_name', $student?->last_name) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email *</label>
        <input type="email" name="email" value="{{ old('email', $student?->email) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $student?->phone) }}" class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Gender *</label>
        <select name="gender" class="input-field" required>
            @foreach (['male', 'female', 'other'] as $genderOption)
                <option value="{{ $genderOption }}" @selected(old('gender', $student?->gender) === $genderOption)>{{ ucfirst($genderOption) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Date of birth</label>
        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student?->date_of_birth?->format('Y-m-d')) }}" class="input-field">
    </div>
</div>
