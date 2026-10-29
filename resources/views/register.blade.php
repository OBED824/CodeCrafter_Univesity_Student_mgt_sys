@extends('layouts.main')
@section('page', 'register')
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
                    <h1 class="text-xl font-bold">Create your student account</h1>
                    <p class="mt-1 text-sm text-slate-500">You will get a registration number straight away. Lecturers and administrators are added by the Registry.</p>
                    <p class="mt-2 text-xs text-[#d97706]">All fields are required.</p>
                </div>
                <p id="reg-error" class="hidden mb-4 text-sm rounded-lg border border-red-100 bg-red-50 text-red-700 px-3 py-2"></p>
                <form id="reg-form" class="space-y-4">
                    <div><label for="name" class="block text-sm font-medium text-slate-600 mb-2">Full name</label><input type="text" id="name" name="name" required minlength="2" autofocus placeholder="Aisha Namuli" class="input-field"></div>
                    <div><label for="email" class="block text-sm font-medium text-slate-600 mb-2">Campus email</label><input type="email" id="email" name="email" required placeholder="aisha.namuli@campus.ac.ug" class="input-field"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label for="gender" class="block text-sm font-medium text-slate-600 mb-2">Gender</label><select id="gender" name="gender" required class="input-field"><option>Female</option><option>Male</option></select></div>
                        <div><label for="deptId" class="block text-sm font-medium text-slate-600 mb-2">Department</label><select id="deptId" name="deptId" required class="input-field"></select></div>
                    </div>
                    <div><label for="password" class="block text-sm font-medium text-slate-600 mb-2">Password</label><input type="password" id="password" name="password" required minlength="8" class="input-field"></div>
                    <div><label for="password_confirmation" class="block text-sm font-medium text-slate-600 mb-2">Confirm password</label><input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" class="input-field"></div>
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Create account</button>
                </form>
                <p class="mt-6 text-center text-sm text-slate-500">Already have an account? <a href="login.html" class="text-[#2563eb] hover:text-[#1d4ed8]">Sign in</a></p>
            </div>
        </div>
    </div>
    
    <!-- Start footer -->
@include('layouts.footer')
    <!-- End footer -->
    
@endsection
