<x-layouts.student title="Lộ Trình Học Tập (Study Plan)">
    <div class="space-y-8">

        <!-- Header Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white p-6 sm:p-8 md:p-10 shadow-xl shadow-indigo-950/10">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-indigo-200">
                        <span>🎯 Personal Study Roadmap</span>
                        <span>•</span>
                        <span>Lộ trình cá nhân hóa</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
                        Lộ Trình Ôn Luyện Bứt Phá
                    </h1>

                    <p class="text-indigo-200 text-xs sm:text-sm leading-relaxed max-w-xl">
                        Mục tiêu nâng từ <strong>Band {{ number_format($progress['band_progress']['current_band'], 1) }}</strong> lên <strong>Band {{ number_format($progress['band_progress']['target_band'], 1) }}</strong> qua phương pháp Lặp lại ngắt quãng (Spaced Repetition) và phòng thi Writing Lab.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-white/15 text-center">
                        <span class="text-xs text-indigo-200 block">Tiến độ tổng thể</span>
                        <span class="text-2xl font-black text-white">{{ $progress['band_progress']['progress_percent'] }}%</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-white/15 text-center">
                        <span class="text-xs text-indigo-200 block">Streak học tập</span>
                        <span class="text-2xl font-black text-amber-300">🔥 {{ $progress['streak']['current_streak'] }} ngày</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones Section -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
            <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                Các Cột Mốc Chinh Phục (Milestones)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Stage 1 -->
                <div class="p-5 rounded-xl border {{ $progress['band_progress']['estimated_band'] >= 5.0 ? 'bg-indigo-50/50 border-indigo-200' : 'bg-slate-50 border-slate-200' }} relative">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $progress['band_progress']['estimated_band'] >= 5.0 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">Giai đoạn 1</span>
                        <span class="text-xs font-semibold text-slate-500">Band 5.0</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1">Nền tảng Từ vựng AWL</h3>
                    <p class="text-xs text-slate-600 mb-3">Học 100+ từ học thuật chủ chốt, loại bỏ lỗi viết tắt informal, đạt tối thiểu 150/250 từ chuẩn.</p>
                    <div class="text-xs font-medium {{ $progress['band_progress']['estimated_band'] >= 5.0 ? 'text-indigo-600' : 'text-slate-400' }}">
                        {{ $progress['band_progress']['estimated_band'] >= 5.0 ? '✓ Đã hoàn thành' : 'Đang thực hiện' }}
                    </div>
                </div>

                <!-- Stage 2 -->
                <div class="p-5 rounded-xl border {{ $progress['band_progress']['estimated_band'] >= 5.5 ? 'bg-indigo-50/50 border-indigo-200' : 'bg-slate-50 border-slate-200' }} relative">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $progress['band_progress']['estimated_band'] >= 5.5 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">Giai đoạn 2</span>
                        <span class="text-xs font-semibold text-slate-500">Band 5.5</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1">Phân đoạn & Overview</h3>
                    <p class="text-xs text-slate-600 mb-3">Bắt buộc có đoạn Overview chuẩn trong Task 1, cấu trúc đoạn văn 3-4 đoạn mạch lạc trong Task 2.</p>
                    <div class="text-xs font-medium {{ $progress['band_progress']['estimated_band'] >= 5.5 ? 'text-indigo-600' : 'text-slate-400' }}">
                        {{ $progress['band_progress']['estimated_band'] >= 5.5 ? '✓ Đã hoàn thành' : 'Đang thực hiện' }}
                    </div>
                </div>

                <!-- Stage 3 -->
                <div class="p-5 rounded-xl border {{ $progress['band_progress']['estimated_band'] >= 6.0 ? 'bg-indigo-50/50 border-indigo-200' : 'bg-slate-50 border-slate-200' }} relative">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $progress['band_progress']['estimated_band'] >= 6.0 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">Giai đoạn 3</span>
                        <span class="text-xs font-semibold text-slate-500">Band 6.0</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1">Đa dạng từ nối & Câu phức</h3>
                    <p class="text-xs text-slate-600 mb-3">Sử dụng ít nhất 4 nhóm từ nối khác nhau, tỷ lệ câu phức & mệnh đề quan hệ $\ge 30\%$.</p>
                    <div class="text-xs font-medium {{ $progress['band_progress']['estimated_band'] >= 6.0 ? 'text-indigo-600' : 'text-slate-400' }}">
                        {{ $progress['band_progress']['estimated_band'] >= 6.0 ? '✓ Đã hoàn thành' : 'Mục tiêu tiếp theo' }}
                    </div>
                </div>

                <!-- Stage 4 -->
                <div class="p-5 rounded-xl border {{ $progress['band_progress']['estimated_band'] >= 6.5 ? 'bg-indigo-50/50 border-indigo-200' : 'bg-slate-50 border-slate-200' }} relative">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $progress['band_progress']['estimated_band'] >= 6.5 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700' }}">Giai đoạn 4</span>
                        <span class="text-xs font-semibold text-slate-500">Band 6.5+</span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm mb-1">Tự nhiên & Chuẩn học thuật</h3>
                    <p class="text-xs text-slate-600 mb-3">Linh hoạt Collocations, độ đa dạng vốn từ TTR cao, lập luận có chiều sâu và hoàn thành đúng thời gian.</p>
                    <div class="text-xs font-medium {{ $progress['band_progress']['estimated_band'] >= 6.5 ? 'text-indigo-600' : 'text-slate-400' }}">
                        {{ $progress['band_progress']['estimated_band'] >= 6.5 ? '✓ Đã hoàn thành' : 'Đích đến bứt phá' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vocabulary Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-lg">
                        📖
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Bài học Từ vựng Tiếp theo</h3>
                    @if($nextLesson)
                        <p class="text-xs text-slate-600">
                            Chủ đề: <strong>{{ $nextLesson->topic?->title }}</strong> — {{ $nextLesson->title }}
                        </p>
                        <div class="flex items-center gap-3 text-xs text-slate-500 pt-1">
                            <span>⏱️ {{ $nextLesson->estimated_minutes ?? 15 }} phút</span>
                            <span>•</span>
                            <span>{{ $nextLesson->items_count ?? $nextLesson->items()->count() }} từ vựng</span>
                        </div>
                    @else
                        <p class="text-xs text-slate-600">Bạn đã hoàn thành tất cả bài học từ vựng!</p>
                    @endif
                </div>

                <div class="pt-6">
                    @if($nextLesson)
                        <a href="{{ route('student.vocabulary.lessons.show', $nextLesson) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                            Bắt đầu học bài này →
                        </a>
                    @else
                        <a href="{{ route('student.vocabulary.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                            Vào Thư viện Từ vựng
                        </a>
                    @endif
                </div>
            </div>

            <!-- Writing Prompt Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                        ✍️
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Đề Luyện Viết Khuyên Dùng</h3>
                    @if($nextPrompt)
                        <p class="text-xs text-slate-600">
                            {{ $nextPrompt->isTask1() ? 'IELTS Task 1' : 'IELTS Task 2' }}: <strong>{{ $nextPrompt->title }}</strong>
                        </p>
                        <div class="flex items-center gap-3 text-xs text-slate-500 pt-1">
                            <span>Mục tiêu: $\ge {{ $nextPrompt->min_words ?? ($nextPrompt->isTask1() ? 150 : 250) }}$ từ</span>
                            <span>•</span>
                            <span>Chủ đề: {{ $nextPrompt->topic?->title ?? 'Tổng hợp' }}</span>
                        </div>
                    @else
                        <p class="text-xs text-slate-600">Chưa có đề viết mới.</p>
                    @endif
                </div>

                <div class="pt-6">
                    @if($nextPrompt)
                        <a href="{{ route('student.writing.practice', $nextPrompt) }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                            Vào Writing Lab luyện viết →
                        </a>
                    @else
                        <a href="{{ route('student.writing.index') }}"
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                            Xem kho đề bài
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-layouts.student>
