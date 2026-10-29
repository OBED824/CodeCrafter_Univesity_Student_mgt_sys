    <footer class="bg-[#0f172a] text-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Student Management System" class="h-12 w-12">
                        <div>
                            <p class="text-base font-semibold text-white">Student Management System</p>
                            <p class="text-[11px] text-slate-400">Campus operations simplified</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-sm">A modern digital hub for registration, results, attendance, fees and academic communication across the campus.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-300 mb-4">Quick links</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="{{ route('index') }}" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('index') }}#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="{{ route('index') }}#roles" class="hover:text-white transition-colors">Who it is for</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-300 mb-4">Departments</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li>Registry</li>
                        <li>Academic Affairs</li>
                        <li>Finance & Bursary</li>
                        <li>Student Services</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-300 mb-4">Support</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="mailto:support@campus.ac.ug" class="hover:text-white">support@campus.ac.ug</a></li>
                        <li>+256 414 000 000</li>
                        <li class="text-[#fbbf24]">Mon–Fri, 8:00am – 5:00pm</li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-400">
                <p>© 2026 <span class="font-semibold text-white">Student Management System</span>. All rights reserved.</p>
                <p>Registry · Academics · Finance · Departments</p>
            </div>
        </div>
    </footer>