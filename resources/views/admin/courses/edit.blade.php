@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Course — {{ $course->course_code }}</h1>

<form method="POST" action="{{ route('admin.courses.update', $course) }}" class="card p-6">
    @csrf
    @method('PUT')
    @include('admin.courses._form', ['course' => $course])
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Update course</button>
        <a href="{{ route('admin.courses.show', $course) }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
