@php
    $layout = $user->isAdmin() ? 'layouts.admin' : 'layouts.student';
@endphp

<x-dynamic-component :component="$layout" title="Hồ sơ cá nhân & Mục tiêu">
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold {{ $user->isAdmin() ? 'text-white' : 'text-slate-900' }} tracking-tight">Cài đặt Hồ sơ cá nhân</h1>
            <p class="text-sm {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-500' }} mt-1">Quản lý thông tin tài khoản, ảnh đại diện, mục tiêu IELTS và bảo mật mật khẩu</p>
        </div>

        <!-- Section 1: Avatar & Basic Information -->
        <div class="{{ $user->isAdmin() ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200' }} border rounded-3xl p-6 sm:p-8 shadow-xs">
            <h2 class="text-lg font-bold {{ $user->isAdmin() ? 'text-white' : 'text-slate-900' }} mb-6 flex items-center gap-2">
                <span>👤</span> Thông tin cá nhân & Ảnh đại diện
            </h2>

            <!-- Avatar upload form -->
            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 mb-6 border-b {{ $user->isAdmin() ? 'border-slate-800' : 'border-slate-100' }}">
                <div class="relative group">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-indigo-500/20 bg-slate-200 shadow-sm">
                </div>

                <div class="flex-1 text-center sm:text-left space-y-3">
                    <h3 class="text-sm font-semibold {{ $user->isAdmin() ? 'text-slate-200' : 'text-slate-800' }}">Ảnh đại diện của bạn</h3>
                    <p class="text-xs {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-500' }}">Hỗ trợ định dạng JPG, PNG, WEBP (Tối đa 2MB)</p>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                            @csrf
                            <label class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer shadow-sm">
                                <span>Tải ảnh mới</span>
                                <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>

                        @if ($user->avatar)
                            <form method="POST" action="{{ route('profile.avatar.delete') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 transition cursor-pointer">
                                    Xóa ảnh
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Info Form -->
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-600' }} mb-1.5">
                            Họ và tên
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border {{ $user->isAdmin() ? 'border-slate-700 bg-slate-800 text-white' : 'border-slate-300 bg-white text-slate-900' }} text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-600' }} mb-1.5">
                            Địa chỉ Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border {{ $user->isAdmin() ? 'border-slate-700 bg-slate-800 text-white' : 'border-slate-300 bg-white text-slate-900' }} text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="min-h-[44px] px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer shadow-sm">
                        Lưu thông tin cá nhân
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: IELTS Goals (Student role) -->
        @if ($user->isStudent())
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs">
                <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <span>🎯</span> Mục tiêu & Lộ trình học IELTS
                </h2>

                <form method="POST" action="{{ route('profile.goals') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="current_band" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Band điểm hiện tại
                            </label>
                            <select
                                id="current_band"
                                name="current_band"
                                required
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                @foreach (['3.0', '3.5', '4.0', '4.5', '5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0'] as $band)
                                    <option value="{{ $band }}" {{ old('current_band', number_format($user->current_band ?? 4.5, 1)) == $band ? 'selected' : '' }}>
                                        Band {{ $band }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="target_band" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Band điểm mục tiêu
                            </label>
                            <select
                                id="target_band"
                                name="target_band"
                                required
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                @foreach (['5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0', '8.5', '9.0'] as $band)
                                    <option value="{{ $band }}" {{ old('target_band', number_format($user->target_band ?? 6.5, 1)) == $band ? 'selected' : '' }}>
                                        Band {{ $band }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="test_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Hình thức thi IELTS
                            </label>
                            <select
                                id="test_type"
                                name="test_type"
                                required
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="academic" {{ old('test_type', $user->test_type?->value ?? 'academic') === 'academic' ? 'selected' : '' }}>
                                    IELTS Academic (Học thuật)
                                </option>
                                <option value="general_training" {{ old('test_type', $user->test_type?->value) === 'general_training' ? 'selected' : '' }}>
                                    IELTS General Training (Tổng quát)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="study_days_per_week" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Số ngày học / tuần
                            </label>
                            <select
                                id="study_days_per_week"
                                name="study_days_per_week"
                                required
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                @for ($i = 1; $i <= 7; $i++)
                                    <option value="{{ $i }}" {{ old('study_days_per_week', $user->study_days_per_week ?? 5) == $i ? 'selected' : '' }}>
                                        {{ $i }} ngày / tuần
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="target_date" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Ngày thi dự kiến
                            </label>
                            <input
                                type="date"
                                id="target_date"
                                name="target_date"
                                value="{{ old('target_date', $user->target_date?->format('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="study_goal" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Ghi chú / Mục tiêu chi tiết
                            </label>
                            <input
                                type="text"
                                id="study_goal"
                                name="study_goal"
                                value="{{ old('study_goal', $user->study_goal) }}"
                                placeholder="Ghi chú mục tiêu học tập..."
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="min-h-[44px] px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer shadow-sm">
                            Cập nhật mục tiêu IELTS
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Section 3: Change Password -->
        <div class="{{ $user->isAdmin() ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200' }} border rounded-3xl p-6 sm:p-8 shadow-xs">
            <h2 class="text-lg font-bold {{ $user->isAdmin() ? 'text-white' : 'text-slate-900' }} mb-6 flex items-center gap-2">
                <span>🔒</span> Đổi mật khẩu
            </h2>

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 max-w-lg">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-600' }} mb-1.5">
                        Mật khẩu hiện tại
                    </label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border {{ $user->isAdmin() ? 'border-slate-700 bg-slate-800 text-white' : 'border-slate-300 bg-white text-slate-900' }} text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-600' }} mb-1.5">
                        Mật khẩu mới
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border {{ $user->isAdmin() ? 'border-slate-700 bg-slate-800 text-white' : 'border-slate-300 bg-white text-slate-900' }} text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider {{ $user->isAdmin() ? 'text-slate-400' : 'text-slate-600' }} mb-1.5">
                        Xác nhận mật khẩu mới
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border {{ $user->isAdmin() ? 'border-slate-700 bg-slate-800 text-white' : 'border-slate-300 bg-white text-slate-900' }} text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div class="pt-2">
                    <button type="submit" class="min-h-[44px] px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer shadow-sm">
                        Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-dynamic-component>
