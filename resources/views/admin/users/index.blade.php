@extends('layouts.app')

@section('title', 'Staff Accounts')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Staff Accounts</h1>
        <p class="text-sm text-slate-500 mt-1">Manage administrator and lecturer access.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Add staff account</a>
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
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No staff accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
