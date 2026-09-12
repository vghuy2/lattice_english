<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Quản trị' }} - Lattice Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex bg-slate-950 text-slate-200 selection:bg-indigo-500 selection:text-white">
    
    <!-- Admin Sidebar -->
    <aside class="w-64 shrink-0 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex min-h-screen sticky top-0">
        <div>
            <!-- Logo / Brand -->
            <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-indigo-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-500/20">
                    L
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-base text-white leading-tight">Lattice IELTS</span>
                    <span class="text-[10px] font-bold text-amber-400 tracking-wider uppercase">Admin Control</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <div class="pt-3 pb-1.5 px-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    Nội dung IELTS
                </div>

                <a href="{{ url('/admin/vocabulary/topics') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/vocabulary*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Quản lý Vocabulary
                </a>

                <a href="{{ url('/admin/writing/prompts') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/writing*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Quản lý Writing
                </a>

                <div class="pt-3 pb-1.5 px-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    Chấm điểm & Hệ thống
                </div>

                <a href="{{ route('admin.writing.scoring.index') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/writing/scoring*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Rubrics & Scoring Rules
                </a>

                <a href="{{ url('/admin/users') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/users*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Quản lý Học viên
                </a>

                <a href="{{ url('/admin/reports') }}"
                   class="min-h-[44px] flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/reports*') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Báo cáo & Thống kê
                </a>
            </nav>
        </div>

        <!-- User bottom section -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 min-h-[44px]">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-lg object-cover ring-1 ring-slate-700 bg-slate-800">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-white max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-amber-400 font-semibold">Administrator</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-[44px] min-w-[44px] p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800/80 rounded-lg transition cursor-pointer" title="Đăng xuất">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen">
        
        <!-- Topbar Mobile & Breadcrumbs -->
        <header class="h-16 border-b border-slate-800 bg-slate-900/80 backdrop-blur-md px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button type="button" onclick="document.getElementById('mobile-admin-menu').classList.toggle('hidden')" class="md:hidden min-h-[44px] min-w-[44px] p-2 text-slate-300 hover:bg-slate-800 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <span class="text-slate-200 font-semibold">{{ $header ?? 'Trang Quản Trị' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Hệ thống hoạt động bình thường
                </span>
            </div>
        </header>

        <!-- Mobile Drawer -->
        <div id="mobile-admin-menu" class="hidden md:hidden bg-slate-900 border-b border-slate-800 p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Dashboard</a>
            <a href="{{ url('/admin/vocabulary/topics') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Quản lý Vocabulary</a>
            <a href="{{ url('/admin/writing/prompts') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Quản lý Writing</a>
            <a href="{{ url('/admin/scoring/rubrics') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Rubrics & Scoring Rules</a>
            <a href="{{ url('/admin/users') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Quản lý Học viên</a>
            <a href="{{ url('/admin/reports') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Báo cáo & Thống kê</a>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-slate-800">Hồ sơ cá nhân</a>
        </div>

        <!-- Page Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <x-alert />
            {{ $slot }}
        </main>
    </div>

</body>
</html>
