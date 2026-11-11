@extends('layouts.app')

@section('title', 'User Accounts')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">User Accounts</h1>
        <p class="text-sm text-slate-500 mt-1">Provision accounts and manage administrator, lecturer, and student roles.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.roles.index') }}" class="btn-outline">Role permissions</a>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Add user account</a>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-2 font-medium">Name</th>
                <th class="px-4 py-2 font-medium">Email</th>
                <th class="px-4 py-2 font-medium">Role</th>
                <th class="px-4 py-2 font-medium">Staff No.</th>
                <th class="px-4 py-2 font-medium">Department</th>
                <th class="px-4 py-2 font-medium">Linked profile</th>
                <th class="px-4 py-2 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($users as $user)
                <tr>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2 capitalize">{{ $user->roles->pluck('name')->join(', ') }}</td>
                    <td class="px-4 py-2">{{ $user->lecturer?->staff_number ?? '—' }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $user->lecturer?->department ?? '—' }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $user->student?->student_number ?? '—' }}</td>
                    <td class="px-4 py-2"><a href="{{ route('admin.users.edit', $user) }}" class="btn-outline">Edit access</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">No user accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
