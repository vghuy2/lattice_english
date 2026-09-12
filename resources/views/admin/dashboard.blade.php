<x-layouts.admin title="Bảng điều khiển Quản trị">
    <div class="space-y-8">
        
        <!-- Top Title Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Tổng quan Hệ thống Quản trị</h1>
                <p class="text-sm text-slate-400 mt-1">Giám sát toàn diện học viên, nội dung IELTS và động cơ chấm điểm tự động Zero AI</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.writing.submissions.index') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                    <span>📝 Xem bài nộp học viên</span>
                </a>
            </div>
        </div>

        <!-- 4 Top Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Metric 1: Total Students -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Học viên</span>
                    <span class="p-2.5 rounded-2xl bg-indigo-500/10 text-indigo-400 text-lg">👥</span>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-mono font-black text-white">{{ $totalStudents }}</p>
                    <div class="mt-1 text-xs text-slate-400">
                        Đang hoạt động trên nền tảng
                    </div>
                </div>
            </div>

            <!-- Metric 2: Vocabulary Items -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Từ vựng IELTS</span>
                    <span class="p-2.5 rounded-2xl bg-violet-500/10 text-violet-400 text-lg">📖</span>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-mono font-black text-white">{{ $totalItems }}</p>
                    <div class="mt-1 text-xs text-slate-400">
                        Trong <strong>{{ $totalLessons }}</strong> bài học ({{ $totalTopics }} chủ đề)
                    </div>
                </div>
            </div>

            <!-- Metric 3: Writing Prompts -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Đề thi Writing</span>
                    <span class="p-2.5 rounded-2xl bg-amber-500/10 text-amber-400 text-lg">📝</span>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-mono font-black text-white">{{ $totalPrompts }}</p>
                    <div class="mt-1 text-xs text-slate-400">
                        Task 1 (Biểu đồ) & Task 2 (Nghị luận)
                    </div>
                </div>
            </div>

            <!-- Metric 4: Writing Submissions -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bài nộp / Đã chấm</span>
                    <span class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-400 text-lg">🎯</span>
                </div>
                <div class="mt-3">
                    <p class="text-3xl font-mono font-black text-emerald-400">{{ $gradedCount }} <span class="text-xs text-slate-400 font-normal">/ {{ $totalSubmissions }} bài</span></p>
                    <div class="mt-1 text-xs text-slate-400">
                        Band TB: <strong>{{ $avgWritingBand ? "Band {$avgWritingBand}" : '--' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Band Distribution & Performance Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Band Distribution Widget (5 Cols) -->
            <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Phân phối Band Điểm Writing</h3>
                    <span class="text-xs text-slate-400">Theo Rubric chuẩn</span>
                </div>

                <div class="space-y-3.5 pt-2">
                    <!-- Band < 5.0 -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300 font-semibold">Dưới Band 5.0</span>
                            <span class="font-mono text-slate-400 font-bold">{{ $bandDistribution['below_5'] }} bài</span>
                        </div>
                        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-rose-500 h-full rounded-full" style="width: {{ $gradedCount > 0 ? ($bandDistribution['below_5'] / $gradedCount) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Band 5.0 - 5.5 -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300 font-semibold">Band 5.0 – 5.5 (Mục tiêu cốt lõi)</span>
                            <span class="font-mono text-amber-400 font-bold">{{ $bandDistribution['band_5_5_5'] }} bài</span>
                        </div>
                        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ $gradedCount > 0 ? ($bandDistribution['band_5_5_5'] / $gradedCount) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Band 6.0 - 6.5 -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300 font-semibold">Band 6.0 – 6.5</span>
                            <span class="font-mono text-emerald-400 font-bold">{{ $bandDistribution['band_6_6_5'] }} bài</span>
                        </div>
                        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $gradedCount > 0 ? ($bandDistribution['band_6_6_5'] / $gradedCount) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Band 7.0+ -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300 font-semibold">Band 7.0+ (Xuất sắc)</span>
                            <span class="font-mono text-indigo-400 font-bold">{{ $bandDistribution['band_7_plus'] }} bài</span>
                        </div>
                        <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $gradedCount > 0 ? ($bandDistribution['band_7_plus'] / $gradedCount) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions Feed (7 Cols) -->
            <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Bài nộp Writing gần đây</h3>
                    <a href="{{ route('admin.writing.submissions.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                        Xem tất cả →
                    </a>
                </div>

                @if ($recentSubmissions->isEmpty())
                    <div class="py-8 text-center text-slate-500 text-xs">
                        Chưa có bài nộp nào từ học viên.
                    </div>
                @else
                    <div class="divide-y divide-slate-800">
                        @foreach ($recentSubmissions as $sub)
                            <div class="py-3.5 flex items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-white">{{ $sub->user?->name ?? 'Học viên' }}</span>
                                        <span class="text-xs text-slate-500 font-mono">({{ $sub->word_count }} từ)</span>
                                    </div>
                                    <p class="text-xs text-slate-400 line-clamp-1">
                                        {{ $sub->prompt?->title }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0">
                                    @if ($sub->isGraded())
                                        <span class="px-2.5 py-1 rounded-full text-xs font-mono font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Band {{ number_format($sub->overall_score, 1) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400">
                                            Đã nộp
                                        </span>
                                    @endif

                                    <a href="{{ route('admin.writing.submissions.show', $sub) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                                        Xem →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Management Sections Shortcuts -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Vocab Admin -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold mb-4">
                        📚
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Quản lý Vocabulary</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Thêm mới chủ đề, bài học và từ vựng chi tiết kèm ví dụ, collocation và ngữ cảnh Writing.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.vocabulary.topics.index') }}" class="min-h-[44px] inline-flex items-center justify-center w-full py-2 px-4 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                        Quản lý Topics & Lessons →
                    </a>
                </div>
            </div>

            <!-- Card 2: Writing Admin -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-violet-500/10 text-violet-400 flex items-center justify-center font-bold mb-4">
                        ✍️
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Quản lý Writing Prompts</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Soạn thảo ngân hàng đề Task 1/Task 2, tải ảnh biểu đồ, dàn ý gợi ý và bài mẫu tham khảo.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.writing.prompts.index') }}" class="min-h-[44px] inline-flex items-center justify-center w-full py-2 px-4 rounded-xl text-xs font-bold text-white bg-violet-600 hover:bg-violet-500 transition">
                        Quản lý Đề thi →
                    </a>
                </div>
            </div>

            <!-- Card 3: Rubrics & Rules -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold mb-4">
                        🎯
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Rubrics & Scoring Rules</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Cấu hình trọng số 4 tiêu chí TR/CC/LR/GRA, bật/tắt các luật chấm điểm trừ/cộng tự động.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.writing.scoring.index') }}" class="min-h-[44px] inline-flex items-center justify-center w-full py-2 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition">
                        Cấu hình Scoring Rules →
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
