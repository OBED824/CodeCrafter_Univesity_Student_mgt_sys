@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')
<h1 class="text-2xl font-bold mb-2">Role Permissions</h1>
<p class="text-slate-500 mb-6">Configure the application permissions granted to each role. Administrator permissions remain full access.</p>

<div class="space-y-6">
    @foreach ($roles as $role)
        <form method="POST" action="{{ route('admin.roles.update', $role->name) }}" class="card p-5">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-semibold capitalize mb-4">{{ $role->name }} permissions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-5">
                @foreach ($permissionsByRole[$role->name] as $permission)
                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            @checked($role->permissions->contains('id', $permission->id))
                            class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ ucfirst($permission->name) }}</span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="btn-primary">Save {{ $role->name }} permissions</button>
        </form>
    @endforeach
</div>
@endsection
