@extends('layouts.app')

@section('title', 'Course Registrations')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Course Registrations</h1>
            <p class="text-sm text-slate-500 mt-1">Create registrations, update their status, or remove a record.</p>
        </div>
        <a href="{{ route('admin.registrations.create') }}" class="btn-primary whitespace-nowrap">+ Add registration</a>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-2 font-medium">Student</th>
                <th class="px-4 py-2 font-medium">Course</th>
                <th class="px-4 py-2 font-medium">Registered</th>
                <th class="px-4 py-2 font-medium">Status</th>
                <th class="px-4 py-2 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($registrations as $registration)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $registration->student->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ $registration->student->student_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $registration->course->course_code }}</p>
                        <p class="text-xs text-slate-500">{{ $registration->course->course_name }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $registration->registered_at?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-3 capitalize">{{ $registration->status }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="status-{{ $registration->id }}">Registration status</label>
                                <select id="status-{{ $registration->id }}" name="status" class="input-field">
                                    @foreach (['pending', 'approved', 'withdrawn'] as $status)
                                        <option value="{{ $status }}" @selected($registration->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-outline whitespace-nowrap">Save</button>
                            </form>
                            <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}" onsubmit="return confirm('Delete this registration?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No course registrations found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $registrations->links() }}
</div>
@endsection
