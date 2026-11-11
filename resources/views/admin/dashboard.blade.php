@extends('layouts.app')

@section('title', 'Administrator Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Administrator Dashboard</h1>

<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('admin.students.index') }}" class="btn-primary">Manage students</a>
    <a href="{{ route('admin.courses.index') }}" class="btn-primary">Manage courses</a>
    <a href="{{ route('admin.registrations.index') }}" class="btn-primary">Manage registrations</a>
    <a href="{{ route('admin.users.index') }}" class="btn-primary">Manage user access</a>
    <a href="{{ route('admin.roles.index') }}" class="btn-outline">Role permissions</a>
    <a href="{{ route('admin.attendance.index') }}" class="btn-outline">Attendance log</a>
    <a href="{{ route('admin.marks.index') }}" class="btn-outline">Gradebook</a>
    <a href="{{ route('admin.reports.index') }}" class="btn-outline">Reports</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="card p-4">
        <p class="text-sm text-slate-500">Active students</p>
        <p class="text-2xl font-bold">{{ $activeStudentCount }}</p>
    </div>
    <div class="card p-4">
        <p class="text-sm text-slate-500">All user accounts</p>
        <p class="text-2xl font-bold">{{ $userCount }}</p>
    </div>
    <div class="card p-4">
        <p class="text-sm text-slate-500">Courses</p>
        <p class="text-2xl font-bold">{{ $courseCount }}</p>
    </div>
    <div class="card p-4">
        <p class="text-sm text-slate-500">Registrations</p>
        <p class="text-2xl font-bold">{{ $registrationCount }}</p>
    </div>
    <div class="card p-4">
        <p class="text-sm text-slate-500">Attendance recorded today</p>
        <p class="text-2xl font-bold">{{ $attendanceCount }}</p>
    </div>
    <div class="card p-4">
        <p class="text-sm text-slate-500">Marks in gradebook</p>
        <p class="text-2xl font-bold">{{ $markCount }}</p>
    </div>
</div>

<h2 class="text-lg font-semibold mb-3">Recently added students</h2>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr><th class="px-4 py-2 font-medium">Name</th><th class="px-4 py-2 font-medium">Student No.</th><th class="px-4 py-2 font-medium">Program</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($recentStudents as $student)
                <tr>
                    <td class="px-4 py-2"><a href="{{ route('admin.students.show', $student) }}" class="text-[#2563eb]">{{ $student->full_name }}</a></td>
                    <td class="px-4 py-2">{{ $student->student_number }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $student->program }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-4 text-slate-500">No students yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
