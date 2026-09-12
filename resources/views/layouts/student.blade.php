<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Học tập' }} - Lattice IELTS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 selection:bg-indigo-600 selection:text-white">
    
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-40 w-full bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Brand & Links -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-600/20 group-hover:scale-105 transition-transform">
                            L
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-base text-slate-900 leading-tight">Lattice IELTS</span>
                            <span class="text-[10px] font-semibold text-indigo-600 tracking-wider uppercase">Student Portal</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('student.dashboard') }}"
                           class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ url('/student/vocabulary') }}"
                           class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->is('student/vocabulary*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Vocabulary
                        </a>
                        <a href="{{ url('/student/writing') }}"
                           class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->is('student/writing*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Writing Lab
                        </a>
                        <a href="{{ url('/student/study-plan') }}"
                           class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->is('student/study-plan*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Study Plan
                        </a>
                        <a href="{{ url('/student/error-notebook') }}"
                           class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium transition {{ request()->is('student/error-notebook*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Sổ tay lỗi sai
                        </a>
                    </div>
                </div>

                <!-- Right Action / Profile -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Target Band Badge -->
                        @if (auth()->user()->target_band)
                            <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200/70 text-indigo-700 text-xs font-semibold">
                                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                                Target: Band {{ number_format(auth()->user()->target_band, 1) }}
                            </div>
                        @endif

                        <!-- Profile Dropdown Button -->
                        <div class="relative flex items-center gap-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition" title="Hồ sơ cá nhân">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-lg object-cover ring-2 ring-indigo-600/20 bg-slate-200">
                                <div class="hidden lg:flex flex-col text-left">
                                    <span class="text-xs font-bold text-slate-800 leading-tight max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                    <span class="text-[10px] text-slate-500 font-medium">Học viên</span>
                                </div>
                            </a>

                            <!-- Logout form -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Đăng xuất">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden min-h-[44px] min-w-[44px] p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-menu" class="hidden md:hidden py-3 border-t border-slate-100 space-y-1">
                <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">
                    Dashboard
                </a>
                <a href="{{ url('/student/vocabulary') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    Vocabulary
                </a>
                <a href="{{ url('/student/writing') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    Writing Lab
                </a>
                <a href="{{ url('/student/study-plan') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    Study Plan
                </a>
                <a href="{{ url('/student/error-notebook') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    Sổ tay lỗi sai
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100">
                    Hồ sơ & Mục tiêu
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-alert />
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 bg-white py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>© {{ date('Y') }} Lattice IELTS Platform. Nền tảng học thuật chuẩn hóa cho Band 4.5 – 5.0 bứt phá.</p>
        </div>
    </footer>

</body>
</html>
