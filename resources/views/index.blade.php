@extends('layouts.main')
@section('content')

    <!-- Start header -->
@include('layouts.header')
    <!-- End header -->

    <!-- Start section -->
    <section class="relative pt-20 bg-mesh">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center py-16 lg:py-20">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-white mb-6"><i data-lucide="calendar" class="w-4 h-4 text-[#d97706]"></i><span class="text-sm text-slate-500">2026 Semester 1 · Registration open</span></div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-5 leading-tight">Every student record,<br><span class="text-gradient">one clear place.</span></h1>
                    <p class="text-sm sm:text-base text-slate-500 max-w-xl mx-auto lg:mx-0 mb-6 leading-relaxed">Manage enrolment, attendance, results and fees. Students see their own progress, lecturers record marks, and the Registry keeps it all in order.</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="register.html" class="group flex items-center gap-2 px-8 py-4 bg-[#2563eb] text-white rounded-xl font-semibold text-sm hover:bg-[#1d4ed8] glow-blue w-full sm:w-auto justify-center">Create student account<i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i></a>
                        <a href="login.html" class="flex items-center justify-center gap-2 px-8 py-4 border border-slate-200 bg-white rounded-xl font-semibold text-sm hover:border-[#2563eb]/40 w-full sm:w-auto">Sign in</a>
                    </div>
                </div>
                <div class="relative w-full max-w-xl mx-auto">
                    <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-br from-blue-200/50 via-sky-100/40 to-amber-200/30 blur-3xl"></div>
                    <div class="relative grid grid-cols-2 gap-4">
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><p class="text-3xl font-bold text-[#2563eb] stat-number" data-stat="students">—</p><p class="text-sm text-slate-500 mt-1">Students</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><p class="text-3xl font-bold text-[#d97706] stat-number" data-stat="courses">—</p><p class="text-sm text-slate-500 mt-1">Course units</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><p class="text-3xl font-bold text-[#d97706] stat-number" data-stat="departments">—</p><p class="text-sm text-slate-500 mt-1">Departments</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><p class="text-3xl font-bold text-[#2563eb] stat-number" data-stat="lecturers">—</p><p class="text-sm text-slate-500 mt-1">Lecturers</p></div>
                        <div class="col-span-2 rounded-3xl border border-slate-200 bg-slate-950 text-white p-5 shadow-2xl hero-panel">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2 text-sm text-slate-200"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>Live campus pulse</div>
                                <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[10px] uppercase tracking-[0.2em] text-sky-200">online</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-3 py-2 border border-white/10">
                                    <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/20 text-blue-200"><i data-lucide="book-check" class="w-4 h-4"></i></span><span class="text-sm text-slate-100">Course registration</span></div>
                                    <span class="text-sm font-semibold text-emerald-300">96%</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-3 py-2 border border-white/10">
                                    <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/20 text-amber-200"><i data-lucide="clipboard-list" class="w-4 h-4"></i></span><span class="text-sm text-slate-100">Attendance compliance</span></div>
                                    <span class="text-sm font-semibold text-amber-300">82%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End section -->

    <!-- Start section -->
    <section id="features" class="py-16 bg-slate-50"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10"><p class="text-xs uppercase tracking-wider text-[#d97706] font-semibold">Features</p><h2 class="text-2xl font-bold mt-2">What the system covers</h2></div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="users" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Student records</h3><p class="text-sm text-slate-500 leading-relaxed">Registration numbers, programmes, guardians and status kept in one searchable register.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="book-open" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Course registration</h3><p class="text-sm text-slate-500 leading-relaxed">Students pick course units within the credit limit; the Registry sets lecturers and departments.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="calendar-check" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Attendance</h3><p class="text-sm text-slate-500 leading-relaxed">Lecturers mark each session in seconds. Students see where they stand against the 75% rule.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="award" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Grades and GPA</h3><p class="text-sm text-slate-500 leading-relaxed">Coursework out of 40, exam out of 60. Letter grades and weighted GPA are worked out automatically.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="wallet" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Fees and payments</h3><p class="text-sm text-slate-500 leading-relaxed">Balances, payment references and an outstanding-fees view for the bursar.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="megaphone" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Announcements</h3><p class="text-sm text-slate-500 leading-relaxed">Post notices to everyone, only students, or only staff.</p></div></div></div></section>
        <!-- End section -->

    <!-- Start section -->
    <section id="roles" class="py-16 bg-white"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10"><p class="text-xs uppercase tracking-wider text-[#d97706] font-semibold">Who it is for</p><h2 class="text-2xl font-bold mt-2">One portal, three views</h2></div>
        <div class="grid md:grid-cols-3 gap-6"><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="graduation-cap" class="w-6 h-6 text-[#2563eb] mb-3"></i><h3 class="font-semibold mb-1">Students</h3><p class="text-sm text-slate-500">Register for courses, read results, track attendance and pay fees.</p></div><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="presentation" class="w-6 h-6 text-[#d97706] mb-3"></i><h3 class="font-semibold mb-1">Lecturers</h3><p class="text-sm text-slate-500">Take attendance and enter marks for the courses you teach.</p></div><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="shield-check" class="w-6 h-6 text-[#2563eb] mb-3"></i><h3 class="font-semibold mb-1">Registry & admin</h3><p class="text-sm text-slate-500">Manage students, courses, departments, fees and announcements.</p></div></div></div></section>
        <!-- End section -->

    <!-- Start footer -->
@include('layouts.footer')
    <!-- End footer -->

@endsection
<!-- End html -->

