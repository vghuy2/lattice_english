<x-layouts.guest>
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/70 p-8 sm:p-10">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tạo tài khoản học viên</h1>
            <p class="text-sm text-slate-500 mt-2">Bắt đầu hành trình bứt phá IELTS Writing & Vocabulary</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Họ và tên
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="Nguyễn Văn A"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

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
                    placeholder="name@example.com"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Mật khẩu
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="Tối thiểu 8 ký tự"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Xác nhận mật khẩu
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    placeholder="Nhập lại mật khẩu"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                >
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full min-h-[44px] flex items-center justify-center py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] transition shadow-md shadow-indigo-600/20 cursor-pointer"
            >
                Đăng ký ngay
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-600">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 hover:underline">
                    Đăng nhập
                </a>
            </p>
        </div>
    </div>
</x-layouts.guest>
