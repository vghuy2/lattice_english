<x-layouts.guest title="Thiết lập mục tiêu IELTS">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-8 sm:p-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 mb-3 font-bold">
                🎯
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Thiết lập mục tiêu học tập</h1>
            <p class="text-sm text-slate-500 mt-1.5">Lattice IELTS sẽ cá nhân hóa lộ trình từ vựng và bài tập Writing theo mục tiêu của bạn</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
            @csrf

            <!-- Form section: Band selection -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Current Band -->
                <div>
                    <label for="current_band" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Band điểm hiện tại của bạn <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="current_band"
                        name="current_band"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm font-medium"
                    >
                        @foreach (['3.0', '3.5', '4.0', '4.5', '5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0'] as $band)
                            <option value="{{ $band }}" {{ old('current_band', '4.5') == $band ? 'selected' : '' }}>
                                Band {{ $band }} {{ $band == '4.5' ? '(Mặc định người mới)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Đánh giá xấp xỉ trình độ hiện tại</p>
                </div>

                <!-- Target Band -->
                <div>
                    <label for="target_band" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Band điểm mục tiêu <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="target_band"
                        name="target_band"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm font-medium"
                    >
                        @foreach (['5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0', '8.5', '9.0'] as $band)
                            <option value="{{ $band }}" {{ old('target_band', '6.5') == $band ? 'selected' : '' }}>
                                Band {{ $band }} {{ $band == '6.5' ? '(Khuyên dùng bứt phá)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Mục tiêu bạn muốn đạt được</p>
                </div>
            </div>

            <!-- Test Type Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-2">
                    Hình thức thi IELTS <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:border-indigo-300 has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900 text-sm">IELTS Academic</span>
                            <input type="radio" name="test_type" value="academic" {{ old('test_type', 'academic') === 'academic' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <span class="text-xs text-slate-500">Phù hợp du học, nghiên cứu đại học & sau đại học. Task 1 phân tích biểu đồ.</span>
                    </label>

                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:border-indigo-300 has-checked:border-indigo-600 has-checked:bg-indigo-50/50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-900 text-sm">IELTS General Training</span>
                            <input type="radio" name="test_type" value="general_training" {{ old('test_type') === 'general_training' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <span class="text-xs text-slate-500">Phù hợp định cư, làm việc quốc tế. Task 1 viết thư (letter).</span>
                    </label>
                </div>
            </div>

            <!-- Target Date & Study Days -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Target Date -->
                <div>
                    <label for="target_date" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Ngày thi dự kiến
                    </label>
                    <input
                        type="date"
                        id="target_date"
                        name="target_date"
                        value="{{ old('target_date') }}"
                        min="{{ date('Y-m-d') }}"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                    >
                    <p class="text-xs text-slate-400 mt-1">Để trống nếu bạn chưa chọn ngày thi cụ thể</p>
                </div>

                <!-- Study Days Per Week -->
                <div>
                    <label for="study_days_per_week" class="block text-sm font-semibold text-slate-800 mb-1.5">
                        Số ngày muốn học mỗi tuần <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="study_days_per_week"
                        name="study_days_per_week"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm font-medium"
                    >
                        @for ($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ old('study_days_per_week', 5) == $i ? 'selected' : '' }}>
                                {{ $i }} ngày / tuần {{ $i == 5 ? '(Khuyên dùng: Đều đặn & hiệu quả)' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Study Goal -->
            <div>
                <label for="study_goal" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Mục tiêu cụ thể hoặc ghi chú cá nhân
                </label>
                <textarea
                    id="study_goal"
                    name="study_goal"
                    rows="3"
                    placeholder="Ví dụ: Cần cải thiện từ vựng chủ đề Environment & cấu trúc câu phức trong Writing Task 2..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                >{{ old('study_goal') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4">
                <button
                    type="submit"
                    class="w-full min-h-[44px] flex items-center justify-center py-3 px-6 rounded-2xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] transition shadow-lg shadow-indigo-600/25 cursor-pointer"
                >
                    Hoàn tất & Bắt đầu học ngay 🚀
                </button>
            </div>
        </form>
    </div>
</x-layouts.guest>
