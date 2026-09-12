<x-layouts.guest>
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/70 p-8 sm:p-10">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Đăng nhập tài khoản</h1>
            <p class="text-sm text-slate-500 mt-2">Truy cập để tiếp tục lộ trình nâng band IELTS</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Địa chỉ Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="name@example.com"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Mật khẩu
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">
                        Quên mật khẩu?
                    </a>
                </div>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input
                    id="remember"
                    name="remember"
                    type="checkbox"
                    value="1"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer"
                >
                <label for="remember" class="ml-2 block text-sm text-slate-600 select-none cursor-pointer">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full min-h-[44px] flex items-center justify-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] transition shadow-md shadow-indigo-600/20 cursor-pointer"
            >
                Đăng nhập
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-600">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">
                    Đăng ký học ngay
                </a>
            </p>
        </div>
    </div>
</x-layouts.guest>
