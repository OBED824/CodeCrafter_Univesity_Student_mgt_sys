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
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 bg-white mb-6"><i data-lucide="calendar" class="w-4 h-4 text-[#d97706]"></i><span class="text-sm text-slate-500">Student and course portal</span></div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-5 leading-tight">Every student record,<br><span class="text-gradient">one clear place.</span></h1>
                    <p class="text-sm sm:text-base text-slate-500 max-w-xl mx-auto lg:mx-0 mb-6 leading-relaxed">Manage student records, courses and course registrations in one place. Students manage their own registrations, lecturers see assigned courses, and administrators oversee the records.</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('register') }}" class="group flex items-center gap-2 px-8 py-4 bg-[#2563eb] text-white rounded-xl font-semibold text-sm hover:bg-[#1d4ed8] glow-blue w-full sm:w-auto justify-center">Create student account<i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i></a>
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 px-8 py-4 border border-slate-200 bg-white rounded-xl font-semibold text-sm hover:border-[#2563eb]/40 w-full sm:w-auto">Sign in</a>
                    </div>
                </div>
                <div class="relative w-full max-w-xl mx-auto">
                    <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-br from-blue-200/50 via-sky-100/40 to-amber-200/30 blur-3xl"></div>
                    <div class="relative grid grid-cols-2 gap-4">
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><div class="flex items-center justify-between mb-3"><p class="text-3xl font-bold text-[#2563eb] stat-number" data-stat="students" data-value="{{ $studentCount }}">{{ $studentCount }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"><i data-lucide="graduation-cap" class="w-5 h-5 text-[#2563eb]"></i></span></div><p class="text-sm text-slate-500 mt-1">Students</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><div class="flex items-center justify-between mb-3"><p class="text-3xl font-bold text-[#d97706] stat-number" data-stat="courses" data-value="{{ $courseCount }}">{{ $courseCount }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"><i data-lucide="book-open" class="w-5 h-5 text-[#d97706]"></i></span></div><p class="text-sm text-slate-500 mt-1">Course units</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><div class="flex items-center justify-between mb-3"><p class="text-3xl font-bold text-[#d97706] stat-number" data-stat="departments" data-value="{{ $departmentCount }}">{{ $departmentCount }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"><i data-lucide="building-2" class="w-5 h-5 text-[#d97706]"></i></span></div><p class="text-sm text-slate-500 mt-1">Departments</p></div>
                        <div class="stat-card rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur-sm"><div class="flex items-center justify-between mb-3"><p class="text-3xl font-bold text-[#2563eb] stat-number" data-stat="lecturers" data-value="{{ $lecturerCount }}">{{ $lecturerCount }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"><i data-lucide="presentation" class="w-5 h-5 text-[#2563eb]"></i></span></div><p class="text-sm text-slate-500 mt-1">Lecturers</p></div>
                        <div class="col-span-2 rounded-3xl border border-slate-200 bg-slate-950 text-white p-5 shadow-2xl hero-panel">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2 text-sm text-slate-200"><span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>Student portal</div>
                                <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[10px] uppercase tracking-[0.2em] text-sky-200">3 roles</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-3 py-2 border border-white/10">
                                    <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/20 text-blue-200"><i data-lucide="book-check" class="w-4 h-4"></i></span><span class="text-sm text-slate-100">Course registration</span></div>
                                    <span class="text-sm font-semibold text-emerald-300">Students</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-3 py-2 border border-white/10">
                                    <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/20 text-amber-200"><i data-lucide="shield-check" class="w-4 h-4"></i></span><span class="text-sm text-slate-100">Role-based access</span></div>
                                    <span class="text-sm font-semibold text-amber-300">Protected</span>
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
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="users" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Student records</h3><p class="text-sm text-slate-500 leading-relaxed">Administrators can create, search, update and manage student profiles.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="book-open" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Course management</h3><p class="text-sm text-slate-500 leading-relaxed">Maintain course units and assign lecturers to the courses they teach.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="book-check" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Course registration</h3><p class="text-sm text-slate-500 leading-relaxed">Students browse courses, register and manage their own enrollments.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="presentation" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-4"><h3 class="font-semibold mb-2">Lecturer workspace</h3><p class="text-sm text-slate-500 leading-relaxed">Lecturers see their assigned courses and the students registered in each.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="shield-check" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Role-based access</h3><p class="text-sm text-slate-500 leading-relaxed">Protected administrator, lecturer and student areas keep access scoped.</p></div><div class="feature-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mb-4"><i data-lucide="layout-dashboard" class="w-5 h-5 text-[#2563eb]"></i></div><h3 class="font-semibold mb-2">Role dashboards</h3><p class="text-sm text-slate-500 leading-relaxed">Each role gets a dashboard focused on its relevant academic records.</p></div></div></div></section>
        <!-- End section -->

    <!-- Start section -->
    <section id="roles" class="py-16 bg-white"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10"><p class="text-xs uppercase tracking-wider text-[#d97706] font-semibold">Who it is for</p><h2 class="text-2xl font-bold mt-2">One portal, three views</h2></div>
        <div class="grid md:grid-cols-3 gap-6"><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="graduation-cap" class="w-6 h-6 text-[#2563eb] mb-3"></i><h3 class="font-semibold mb-1">Students</h3><p class="text-sm text-slate-500">Browse courses, manage your registrations and view your own academic dashboard.</p></div><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="presentation" class="w-6 h-6 text-[#d97706] mb-3"></i><h3 class="font-semibold mb-1">Lecturers</h3><p class="text-sm text-slate-500">View the courses assigned to you and the students registered in them.</p></div><div class="role-card rounded-2xl border border-slate-200 bg-white p-6"><i data-lucide="shield-check" class="w-6 h-6 text-[#2563eb] mb-3"></i><h3 class="font-semibold mb-1">Registry & admin</h3><p class="text-sm text-slate-500">Manage student records, courses and course registrations.</p></div></div></div></section>
        <!-- End section -->

    <!-- Start footer -->
@include('layouts.footer')
    <!-- End footer -->

@endsection

@section('scripts')
    <script>
        // Count each stat card up from 0 to its real number when it appears on screen.
        (function () {
            const counters = document.querySelectorAll('[data-stat]');
            const duration = 800;

            const countUp = (el) => {
                const target = Number(el.dataset.value || 0);
                const start = performance.now();
                const step = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.round(target * eased);
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };
                requestAnimationFrame(step);
                // Safety net: the animation is paused on hidden tabs, so make
                // sure the real number is shown even if the frames never run.
                setTimeout(() => { el.textContent = target; }, duration + 100);
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        countUp(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            counters.forEach((el) => {
                el.textContent = '0';
                observer.observe(el);
            });
        })();
    </script>
@endsection
<!-- End html -->
