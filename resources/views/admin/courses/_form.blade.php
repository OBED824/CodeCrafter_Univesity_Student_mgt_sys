{{-- Shared course form fields. Expects $course (null when creating) and $lecturers. --}}
@php($course = $course ?? null)

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Course code *</label>
        <input type="text" name="course_code" value="{{ old('course_code', $course?->course_code) }}" class="input-field" placeholder="e.g. CSC101" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Responsible lecturer</label>
        <select name="lecturer_id" class="input-field">
            <option value="">— Unassigned —</option>
            @foreach ($lecturers as $lecturerOption)
                <option value="{{ $lecturerOption->id }}" @selected((int) old('lecturer_id', $course?->lecturer_id) === $lecturerOption->id)>
                    {{ $lecturerOption->full_name }} ({{ $lecturerOption->department }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Course name *</label>
        <input type="text" name="course_name" value="{{ old('course_name', $course?->course_name) }}" class="input-field" required>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="input-field">{{ old('description', $course?->description) }}</textarea>
    </div>
</div>
