    <header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="index.html" class="flex items-center gap-3">
                    <img src="assets/img/logo.jpeg" alt="Student Management System" class="h-11 w-11">
                    <div class="hidden sm:block"><p class="text-sm font-semibold text-slate-900 leading-tight">Student Management System</p><p class="text-[11px] text-slate-500">Records, results and fees in one place</p></div>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('index') }}" class="text-slate-900 text-sm font-medium">Home</a>
                    <a href="index.html#features" class="text-slate-500 hover:text-slate-900 transition-colors text-sm font-medium">Features</a>
                    <a href="index.html#roles" class="text-slate-500 hover:text-slate-900 transition-colors text-sm font-medium">Who it is for</a>
                    <a href="{{ route('contact')}}" class="text-slate-500 hover:text-slate-900 transition-colors text-sm font-medium">Contact us</a>
                    <a href="{{ route('contactlist')}}" class="text-slate-500 hover:text-slate-900 transition-colors text-sm font-medium">Contact list</a>
                    <a href="{{ route('login')}}" class="px-5 py-2.5 border border-slate-200 text-slate-900 rounded-lg font-medium text-sm hover:border-[#2563eb]/40">Sign in</a>
                    <a href="{{ route('register')}}" class="px-5 py-2.5 bg-[#2563eb] text-white rounded-lg font-medium text-sm hover:bg-[#1d4ed8] glow-blue">Create account</a>
                </div>

                <!-- Start details -->
                <details class="menu-dropdown relative md:hidden">
                    <summary aria-label="Open navigation menu" class="p-2 text-slate-900 rounded-lg hover:bg-slate-100 cursor-pointer">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </summary>
                    
                    <!-- Start nav -->
                    <nav aria-label="Mobile navigation" class="absolute right-0 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('index') }}" aria-current="page" class="rounded-lg px-3 py-2 text-sm font-medium text-[#2563eb] hover:bg-blue-50">Home</a>
                            <a href="index.html#features" class="rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Features</a>
                            <a href="index.html#roles" class="rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Who it is for</a>
                            <a href="{{ route('contact')}}" class="rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Contact us</a>
                            <a href="{{ route('contactlist')}}" class="rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Contact list</a>
                            <a href="{{ route('login')}}" class="rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Sign in</a>
                            <a href="{{ route('register')}}" class="mt-1 rounded-lg bg-[#2563eb] px-3 py-2 text-center text-sm font-medium text-white hover:bg-[#1d4ed8]">Create account</a>
                        </div>
                    </nav>
                    <!-- End nav -->
                    
                </details>
                <!-- End details -->

            </div>
        </div>
    </header>