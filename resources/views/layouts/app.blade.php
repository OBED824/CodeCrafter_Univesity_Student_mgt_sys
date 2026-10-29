<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Student Management System</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/img/logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .input-field { width: 100%; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.625rem 0.875rem; font-size: 0.875rem; outline: none; }
        .input-field:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
        .btn-primary { display: inline-block; background: #2563eb; color: #fff; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-danger { display: inline-block; background: #dc2626; color: #fff; border-radius: 0.75rem; padding: 0.5rem 1rem; font-size: 0.8125rem; font-weight: 600; }
        .btn-outline { display: inline-block; border: 1px solid #e2e8f0; color: #0f172a; border-radius: 0.75rem; padding: 0.5rem 1rem; font-size: 0.8125rem; font-weight: 500; }
        .btn-outline:hover { border-color: #2563eb; color: #2563eb; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; }
        .nav-link { display: block; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; color: #475569; }
        .nav-link:hover, .nav-link.active { background: #eff6ff; color: #2563eb; }
        .error-text { color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased" style="font-family: Inter, system-ui, sans-serif;">

<div class="min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/img/logo.jpeg') }}" alt="Logo" class="h-9 w-9 rounded">
                <p class="font-semibold text-sm hidden sm:block">Student Management System</p>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm text-slate-500 hidden sm:inline">{{ auth()->user()->name }}
                        (<span class="font-medium">{{ auth()->user()->roles->pluck('name')->first() }}</span>)</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-outline">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline">Sign in</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="flex-1 flex">
        <!-- Sidebar: links depend on the logged-in user's role -->
        @auth
            <aside class="hidden md:block w-56 bg-white border-r border-slate-200 p-4 space-y-1">
                @if (auth()->user()->hasRole('administrator'))
                    <p class="text-xs font-semibold text-slate-400 uppercase px-3 pt-2 pb-1">Administrator</p>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('admin.students.index') }}" class="nav-link">Students</a>
                    <a href="{{ route('admin.courses.index') }}" class="nav-link">Courses</a>
                @elseif (auth()->user()->hasRole('lecturer'))
                    <p class="text-xs font-semibold text-slate-400 uppercase px-3 pt-2 pb-1">Lecturer</p>
                    <a href="{{ route('lecturer.dashboard') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('lecturer.courses') }}" class="nav-link">My Courses</a>
                @elseif (auth()->user()->hasRole('student'))
                    <p class="text-xs font-semibold text-slate-400 uppercase px-3 pt-2 pb-1">Student</p>
                    <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('student.courses') }}" class="nav-link">Available Courses</a>
                    <a href="{{ route('student.registrations.index') }}" class="nav-link">My Registrations</a>
                @endif
            </aside>
        @endauth

        <!-- Main content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-5xl mx-auto">

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                        <p class="font-semibold mb-1">Please fix the following:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        Student Management System — University of {{ date('Y') }}
    </footer>
</div>
</body>
</html>
