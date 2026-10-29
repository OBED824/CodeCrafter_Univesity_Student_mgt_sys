@extends('layouts.app')

@section('title', 'Add Course')

@section('content')
<h1 class="text-2xl font-bold mb-6">Add Course</h1>

<form method="POST" action="{{ route('admin.courses.store') }}" class="card p-6">
    @csrf
    @include('admin.courses._form')
    <div class="mt-6 flex gap-2">
        <button type="submit" class="btn-primary">Save course</button>
        <a href="{{ route('admin.courses.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
