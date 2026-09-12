<x-layouts.student title="Dashboard Học viên">
    <div class="space-y-8">

        <!-- Welcome Hero Section -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white p-6 sm:p-8 md:p-10 shadow-xl shadow-indigo-950/10">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-indigo-200">
                        <span>✨ Lộ trình bứt phá IELTS Band 4.5 – 5.0+</span>
                        <span>•</span>
                        <span>{{ $user->test_type?->label() ?? 'IELTS Academic' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
                        Xin chào, {{ $user->name }}! 👋
                    </h1>

                    <p class="text-indigo-200 text-xs sm:text-sm leading-relaxed max-w-xl">
                        Theo dõi tiến độ từ vựng học thuật, rèn luyện kỹ năng Writing với bộ luật chấm điểm chuẩn IELTS minh bạch.
                    </p>

                    <!-- Goal Overview Pills -->
                    <div class="pt-2 flex flex-wrap items-center gap-3 text-xs">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10">
                            <span class="text-indigo-300">Xuất phát điểm:</span>
                            <span class="font-bold text-white">Band {{ number_format($band_progress['current_band'], 1) }}</span>
                        </div>

                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20">
                            <span class="text-indigo-300">Mục tiêu:</span>
                            <span class="font-black text-amber-300">Band {{ number_format($band_progress['target_band'], 1) }} 🚀</span>
                        </div>

                        @if ($user->target_date)
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10">
                                <span class="text-indigo-300">Dự kiến thi:</span>
                                <span class="font-bold text-white">{{ $user->target_date->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Study Streak Badge -->
                <div class="bg-white/10 backdrop-blur-md p-5 rounded-3xl border border-white/15 text-center min-w-[180px] shrink-0 self-start lg:self-center">
                    <span class="text-3xl block mb-1">🔥</span>
                    <span class="text-2xl font-black font-mono text-amber-300">{{ $streak['current_streak'] }}</span>
                    <span class="text-xs text-indigo-200 font-semibold block mt-0.5">ngày liên tục</span>
                    <div class="mt-3 pt-3 border-t border-white/10 text-[11px] text-indigo-300">
                        Tổng cộng: <strong>{{ $streak['active_days_count'] }}</strong> ngày học
                    </div>
                </div>
            </div>

            <!-- Background subtle glow -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- Action Alerts Banner (Due reviews or in-progress) -->
        @if ($vocab['due_review_count'] > 0)
            <div class="bg-gradient-to-r from-amber-500/15 via-amber-500/10 to-orange-500/10 border border-amber-500/30 rounded-3xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-800 flex items-center justify-center text-2xl font-bold shrink-0">
                        ⚡
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">
                            Bạn có <strong class="text-amber-700 font-mono font-black text-base">{{ $vocab['due_review_count'] }}</strong> từ vựng cần ôn tập hôm nay!
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Áp dụng phương pháp lặp lại ngắt quãng (Spaced Repetition) để nhớ từ vựng vĩnh viễn vào trí nhớ dài hạn.
                        </p>
                    </div>
                </div>

                <a href="{{ route('student.vocabulary.review.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition shrink-0 cursor-pointer">
                    <span>🧠 Bắt đầu ôn tập ngay</span>
                </a>
            </div>
        @endif

        <!-- 4 Key Core Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Stat 1: Vocab -->
            <div class="bg-white p-5.5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Từ vựng đã học</span>
                        <div class="flex items-baseline gap-1.5 mt-1">
                            <span class="text-2xl font-mono font-black text-slate-900">{{ $vocab['total_learned'] }}</span>
                            <span class="text-xs text-slate-400 font-medium">từ</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                        📚
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-semibold">{{ $vocab['mastered_count'] }} thành thạo</span>
                    <span class="text-slate-400">•</span>
                    <span class="text-amber-700 font-semibold">{{ $vocab['learning_count'] }} đang học</span>
                </div>
            </div>

            <!-- Stat 2: Writing Submissions -->
            <div class="bg-white p-5.5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bài viết Writing</span>
                        <div class="flex items-baseline gap-1.5 mt-1">
                            <span class="text-2xl font-mono font-black text-slate-900">{{ $writing['total_submitted'] }}</span>
                            <span class="text-xs text-slate-400 font-medium">bài đã nộp</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl font-bold">
                        ✍️
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Task 1: <strong>{{ $writing['task1_count'] }}</strong></span>
                    <span class="text-slate-400">•</span>
                    <span class="text-slate-600 font-medium">Task 2: <strong>{{ $writing['task2_count'] }}</strong></span>
                </div>
            </div>

            <!-- Stat 3: Average Band Score -->
            <div class="bg-white p-5.5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Band Writing Ước tính</span>
                        <div class="flex items-baseline gap-1.5 mt-1">
                            @if ($writing['avg_overall_band'])
                                <span class="text-2xl font-mono font-black text-emerald-600">Band {{ number_format($writing['avg_overall_band'], 1) }}</span>
                            @else
                                <span class="text-2xl font-mono font-black text-slate-300">--</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        🏆
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                    @if ($writing['graded_count'] > 0)
                        <span>Dựa trên {{ $writing['graded_count'] }} bài đã chấm Rubric</span>
                    @else
                        <span>Chưa có bài viết nào được chấm</span>
                    @endif
                </div>
            </div>

            <!-- Stat 4: Lessons Progress -->
            <div class="bg-white p-5.5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tiến độ bài học</span>
                        <div class="flex items-baseline gap-1.5 mt-1">
                            <span class="text-2xl font-mono font-black text-slate-900">{{ $vocab['completed_lessons'] }}</span>
                            <span class="text-xs text-slate-400 font-medium">/ {{ $vocab['total_lessons'] }} bài</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        🎯
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $vocab['progress_percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Activity & Consistency Heatmap -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Hoạt động trong tuần</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Duy trì việc luyện tập từ vựng & writing mỗi ngày để đạt hiệu quả cao nhất.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs text-slate-500">Đã học</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-200 ml-2"></span>
                    <span class="text-xs text-slate-400">Chưa học</span>
                </div>
            </div>

            <!-- 7 Days Grid -->
            <div class="grid grid-cols-7 gap-2 sm:gap-4 mt-5">
                @foreach ($streak['weekly_activity'] as $date => $item)
                    <div class="flex flex-col items-center gap-2 p-3 rounded-2xl {{ $item['is_today'] ? 'bg-indigo-50 border border-indigo-200' : 'bg-slate-50' }}">
                        <span class="text-[11px] font-bold uppercase {{ $item['is_today'] ? 'text-indigo-700' : 'text-slate-400' }}">
                            {{ $item['day_name'] }}
                        </span>
                        
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $item['is_active'] ? 'bg-emerald-500 text-white shadow-xs' : ($item['is_today'] ? 'border-2 border-dashed border-indigo-400 text-indigo-700' : 'bg-slate-200 text-slate-400') }}">
                            @if ($item['is_active'])
                                ✓
                            @else
                                •
                            @endif
                        </div>

                        <span class="text-[10px] font-mono text-slate-400">
                            {{ \Carbon\Carbon::parse($date)->format('d/m') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Workspace Split Grid (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Resume In-Progress & Quick Hubs (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Resume Learning Card -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🚀 Tiếp tục lộ trình</span>
                    </h3>

                    <div class="space-y-3">
                        <!-- Next / In-progress Lesson -->
                        @if ($in_progress['lesson'])
                            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-200/80 text-indigo-900">
                                            Bài học Từ vựng
                                        </span>
                                        <span class="text-xs text-slate-500">{{ $in_progress['lesson']->topic?->title }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        {{ $in_progress['lesson']->title }}
                                    </h4>
                                </div>

                                <a href="{{ route('student.vocabulary.lessons.show', $in_progress['lesson']) }}" class="min-h-[38px] inline-flex items-center px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition shrink-0">
                                    Học tiếp →
                                </a>
                            </div>
                        @endif

                        <!-- In-progress Writing Draft -->
                        @if ($in_progress['draft'])
                            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-200 text-amber-900">
                                            Bản nháp Writing dở dang
                                        </span>
                                        <span class="text-xs text-slate-500 font-mono">{{ $in_progress['draft']->word_count }} từ</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-900">
                                        {{ $in_progress['draft']->prompt?->title }}
                                    </h4>
                                </div>

                                <a href="{{ route('student.writing.practice', $in_progress['draft']->prompt) }}" class="min-h-[38px] inline-flex items-center px-4 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition shrink-0">
                                    Viết tiếp →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hub Exploration Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Vocab Hub -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs flex flex-col justify-between space-y-4 hover:border-indigo-300 transition">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-3">
                                📖
                            </div>
                            <h4 class="font-extrabold text-sm text-slate-900">Thư viện Từ vựng</h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Tra cứu từ vựng theo chủ đề, học từ qua flashcard, làm quiz 5 dạng bài tập.
                            </p>
                        </div>
                        <a href="{{ route('student.vocabulary.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                            Xem thư viện →
                        </a>
                    </div>

                    <!-- Writing Lab -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs flex flex-col justify-between space-y-4 hover:border-indigo-300 transition">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center text-xl mb-3">
                                ✍️
                            </div>
                            <h4 class="font-extrabold text-sm text-slate-900">Phòng Luyện Writing</h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Luyện viết Task 1 & Task 2 với đồng hồ bấm giờ, thước đo số từ và chấm điểm 4 tiêu chí.
                            </p>
                        </div>
                        <a href="{{ route('student.writing.index') }}" class="text-xs font-bold text-violet-600 hover:text-violet-700 flex items-center gap-1">
                            Vào phòng luyện →
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Column: Recent Submissions & Vocabulary Notebook (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Recent Writing Submissions -->
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-extrabold text-slate-900">Bài viết gần đây</h3>
                        <a href="{{ route('student.writing.submissions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                            Xem tất cả →
                        </a>
                    </div>

                    @if ($writing['recent_submissions']->isEmpty())
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Chưa có bài viết nào được nộp. Hãy bắt đầu bài viết đầu tiên!
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($writing['recent_submissions'] as $sub)
                                <div class="py-3.5 flex items-center justify-between gap-3">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sub->prompt?->task_type?->badgeClass() ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $sub->prompt?->task_type?->label() }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">{{ $sub->word_count }} từ</span>
                                        </div>
                                        <a href="{{ route('student.writing.submissions.show', $sub) }}" class="text-xs font-bold text-slate-800 hover:text-indigo-600 transition block line-clamp-1">
                                            {{ $sub->prompt?->title }}
                                        </a>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        @if ($sub->isGraded())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-black bg-emerald-100 text-emerald-800">
                                                Band {{ number_format($sub->overall_score, 1) }}
                                            </span>
                                        @elseif ($sub->isDraft())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                                Nháp
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                                Đã nộp
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Vocabulary Saved / Favorite Words Box -->
                <div class="bg-gradient-to-br from-indigo-50 to-slate-50 border border-indigo-100 rounded-3xl p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">⭐</span>
                            <h4 class="font-extrabold text-sm text-slate-900">Sổ tay Từ vựng yêu thích</h4>
                        </div>
                        <span class="text-xs font-mono font-bold text-indigo-700">{{ $vocab['saved_count'] }} từ đã lưu</span>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Lưu các từ vựng band cao, collocations hay gặp trong bài đọc/viết để ôn luyện thường xuyên.
                    </p>

                    <a href="{{ route('student.vocabulary.review.index', ['filter' => 'favorites']) }}" class="min-h-[40px] w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 font-bold text-xs transition shadow-xs">
                        <span>Mở Sổ tay từ vựng yêu thích →</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</x-layouts.student>
