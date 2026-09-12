<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lattice IELTS' }} - Luyện thi IELTS Thông Minh</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-between bg-gradient-to-br from-slate-50 via-indigo-50/30 to-slate-100 selection:bg-indigo-600 selection:text-white">
    
    <!-- Header -->
    <header class="w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    L
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-lg text-slate-900 leading-none tracking-tight">Lattice IELTS</span>
                    <span class="text-[11px] font-medium text-indigo-600 tracking-wider uppercase mt-0.5">Writing & Vocab Band 5.0+</span>
                </div>
            </a>
            
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Vào học tập
                        </a>
                    @else
                        @if (request()->routeIs('login'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                Đăng ký tài khoản
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">
                                Đã có tài khoản? Đăng nhập
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <x-alert />
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 bg-white/50 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            <p>© {{ date('Y') }} Lattice IELTS Platform. Nền tảng luyện từ vựng & chấm Writing chuẩn luật học thuật.</p>
        </div>
    </footer>

</body>
</html>
