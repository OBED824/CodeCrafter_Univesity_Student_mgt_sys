@extends('layouts.app')

@section('title', 'Add Student')

@section('content')
<h1 class="text-2xl font-bold mb-6">Add Student</h1>

<form method="POST" action="{{ route('admin.students.store') }}" class="card p-6">
    @csrf
    @include('admin.students._form')
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Save student</button>
        <a href="{{ route('admin.students.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
