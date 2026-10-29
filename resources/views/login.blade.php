@extends('layouts.main')
@section('content')
    
    <!-- Start header -->
@include('layouts.header')
    <!-- End header -->
    
    <div class="flex-1 flex items-center justify-center p-4 bg-white">
        <div class="w-full max-w-md">
            <div class="flex justify-center mb-8">
                <a href="index.html" class="inline-flex flex-col items-center gap-3">
                    <img src="assets/img/logo.jpeg" alt="Student Management System" class="h-16 w-16">
                    <span class="text-sm font-semibold text-slate-900">Student Management System</span>
                </a>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-slate-900">Sign in</h1>
                    <p class="mt-1 text-sm text-slate-500">Use your campus email to see your courses, results and fees.</p>
                </div>
                <p id="login-error" class="hidden mb-4 text-sm rounded-lg border border-red-100 bg-red-50 text-red-700 px-3 py-2"></p>
                <form id="login-form" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-600 mb-2">Campus email</label>
                        <input type="email" id="email" name="email" required autofocus placeholder="you@campus.ac.ug" class="input-field">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-600 mb-2">Password</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="input-field">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="h-4 w-4 rounded border-slate-300"><span class="text-sm text-slate-500">Keep me signed in</span></label>
                        <a href="forgot-password.html" class="text-sm font-medium text-[#2563eb] hover:text-[#1d4ed8]">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Sign in</button>
                </form>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-xs text-slate-500 mb-3 text-center">Prototype: try a demo account (any password works)</p>
                    <div class="grid grid-cols-3 gap-2">
                        <a href="#" data-demo="student" class="text-center text-xs px-2 py-2 rounded-lg border border-slate-200 bg-white hover:border-[#2563eb]">Student</a>
                        <a href="#" data-demo="staff" class="text-center text-xs px-2 py-2 rounded-lg border border-slate-200 bg-white hover:border-[#d97706]">Lecturer</a>
                        <a href="#" data-demo="admin" class="text-center text-xs px-2 py-2 rounded-lg border border-slate-200 bg-white hover:border-[#2563eb]">Admin</a>
                    </div>
                </div>
                <p class="mt-6 text-center text-sm text-slate-500">New student? <a href="register.html" class="text-[#2563eb] hover:text-[#1d4ed8]">Create an account</a></p>
            </div>
        </div>
    </div>
    
    <!-- Start footer -->
@include('layouts.footer')
    <!-- End footer -->
@endsection
