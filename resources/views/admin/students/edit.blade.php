@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Student — {{ $student->full_name }}</h1>

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="card p-6">
    @csrf
    @method('PUT')
    @include('admin.students._form', ['student' => $student])
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Update student</button>
        <a href="{{ route('admin.students.show', $student) }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
