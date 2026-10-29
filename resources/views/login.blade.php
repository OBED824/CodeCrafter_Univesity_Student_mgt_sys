@extends('layouts.main')

@section('content')

@include('layouts.header')

<div class="flex-1 flex items-center justify-center p-4 bg-white">
    <div class="w-full max-w-md">
        <div class="flex justify-center mb-8">
            <a href="{{ route('index') }}" class="inline-flex flex-col items-center gap-3">
                <img src="assets/img/logo.jpeg" alt="Student Management System" class="h-16 w-16">
                <span class="text-sm font-semibold text-slate-900">Student Management System</span>
            </a>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-xl font-bold text-slate-900">Sign in</h1>
                <p class="mt-1 text-sm text-slate-500">Use your campus email to see your courses and registrations.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 text-sm rounded-lg border border-red-100 bg-red-50 text-red-700 px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 text-sm rounded-lg border border-emerald-100 bg-emerald-50 text-emerald-700 px-3 py-2">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-2">Campus email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@campus.ac.ug" class="input-field">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-600 mb-2">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" class="input-field">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300">
                        <span class="text-sm text-slate-500">Keep me signed in</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Sign in</button>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')

@endsection
