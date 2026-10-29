@extends('layouts.main')

@section('page', 'register')

@section('content')

@include('layouts.header')

<div class="flex-1 flex items-center justify-center p-4 bg-white">
    <div class="w-full max-w-md">
        <div class="flex justify-center mb-8">
            <a href="{{ route('index') }}" class="inline-flex flex-col items-center gap-3">
                <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Student Management System" class="h-16 w-16">
                <span class="text-sm font-semibold text-slate-900">Student Management System</span>
            </a>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-xl font-bold">Create your student account</h1>
                <p class="mt-1 text-sm text-slate-500">You will get a registration number straight away. Lecturers and administrators are added by the Registry.</p>
                <p class="mt-2 text-xs text-[#d97706]">Fields marked * are required.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 text-sm rounded-lg border border-red-100 bg-red-50 text-red-700 px-3 py-2">
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-600 mb-2">First name *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required minlength="2" autofocus placeholder="Aisha" class="input-field">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-600 mb-2">Last name *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required minlength="2" placeholder="Namuli" class="input-field">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-2">Campus email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="aisha.namuli@campus.ac.ug" class="input-field">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-slate-600 mb-2">Gender *</label>
                        <select id="gender" name="gender" required class="input-field">
                            @foreach (['female', 'male', 'other'] as $genderOption)
                                <option value="{{ $genderOption }}" @selected(old('gender') === $genderOption)>{{ ucfirst($genderOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-slate-600 mb-2">Date of birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" class="input-field">
                    </div>
                </div>
                <div>
                    <label for="program" class="block text-sm font-medium text-slate-600 mb-2">Programme / course of study *</label>
                    <input type="text" id="program" name="program" value="{{ old('program') }}" required placeholder="BSc Computer Science" class="input-field">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-600 mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+256 7xx xxxxxx" class="input-field">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-600 mb-2">Password *</label>
                    <input type="password" id="password" name="password" required minlength="8" class="input-field">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-600 mb-2">Confirm password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" class="input-field">
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Create account</button>
            </form>
            <p class="mt-6 text-center text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}" class="text-[#2563eb] hover:text-[#1d4ed8]">Sign in</a></p>
        </div>
    </div>
</div>

@include('layouts.footer')

@endsection
