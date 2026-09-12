<x-layouts.student title="IELTS Writing Lab">
    <div class="space-y-8">

        <!-- Header & Top Stats Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl shadow-indigo-950/10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-indigo-200 mb-3">
                    <span>✍️ IELTS Writing Lab</span>
                    <span>•</span>
                    <span>Luyện viết Task 1 & Task 2</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Phòng Luyện viết IELTS Writing</h1>
                <p class="text-indigo-200 text-sm mt-1 max-w-xl leading-relaxed">
                    Luyện tập các dạng bài thực tế, xem dàn ý chi tiết, phân tích bài mẫu chuẩn band và nhận đánh giá theo 4 tiêu chí IELTS.
                </p>
            </div>

            <!-- Stats Overview -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <span class="text-[11px] font-medium text-indigo-200 block uppercase tracking-wider">Đã viết</span>
                    <span class="text-xl font-bold text-white font-mono">{{ $totalSubmitted }} <span class="text-xs font-normal text-indigo-200">bài</span></span>
                </div>

                <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center min-w-[100px]">
                    <span class="text-[11px] font-medium text-indigo-200 block uppercase tracking-wider">Task 1 / Task 2</span>
                    <span class="text-xl font-bold text-white font-mono">{{ $task1Count }} / {{ $task2Count }}</span>
                </div>

                @if ($avgScore)
                    <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center min-w-[100px]">
                        <span class="text-[11px] font-medium text-amber-300 block uppercase tracking-wider">Band TB</span>
                        <span class="text-xl font-black text-amber-300 font-mono">{{ $avgScore }}</span>
                    </div>
                @endif

                <a href="{{ route('student.writing.submissions.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-xs font-bold text-slate-900 bg-amber-300 hover:bg-amber-200 transition shadow-sm">
                    <span>📖 Lịch sử bài viết</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 shadow-xs">
            <form method="GET" action="{{ route('student.writing.index') }}" class="flex flex-col lg:flex-row items-center gap-3">
                
                <!-- Task Type Tabs -->
                <div class="flex items-center bg-slate-100 p-1 rounded-2xl w-full lg:w-auto shrink-0">
                    <a href="{{ route('student.writing.index', array_merge(request()->query(), ['task_type' => ''])) }}"
                       class="flex-1 lg:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ empty($selectedTaskType) ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tất cả Task
                    </a>
                    <a href="{{ route('student.writing.index', array_merge(request()->query(), ['task_type' => 'task_1'])) }}"
                       class="flex-1 lg:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ $selectedTaskType === 'task_1' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Task 1 (Biểu đồ)
                    </a>
                    <a href="{{ route('student.writing.index', array_merge(request()->query(), ['task_type' => 'task_2'])) }}"
                       class="flex-1 lg:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ $selectedTaskType === 'task_2' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Task 2 (Nghị luận)
                    </a>
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm đề bài theo tiêu đề hoặc từ khóa..."
                        class="w-full min-h-[44px] pl-10 pr-4 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Level Selector -->
                <select name="level" class="w-full lg:w-40 min-h-[44px] px-3 rounded-2xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="">Tất cả trình độ</option>
                    @foreach ($levels as $lv)
                        <option value="{{ $lv->value }}" @selected($selectedLevel === $lv->value)>
                            {{ $lv->shortLabel() }}
                        </option>
                    @endforeach
                </select>

                <!-- Topic Selector -->
                <select name="topic_id" class="w-full lg:w-44 min-h-[44px] px-3 rounded-2xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="">Tất cả chủ đề</option>
                    @foreach ($topics as $tp)
                        <option value="{{ $tp->id }}" @selected($selectedTopic == $tp->id)>
                            {{ $tp->title }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="min-h-[44px] px-5 py-2.5 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition w-full lg:w-auto">
                    Lọc
                </button>
            </form>
        </div>

        <!-- Prompts Grid -->
        @if ($prompts->isEmpty())
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 flex items-center justify-center text-2xl text-indigo-600 mb-3">
                    ✍️
                </div>
                <h3 class="text-base font-bold text-slate-900">Không tìm thấy đề bài phù hợp</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Hãy thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($prompts as $prompt)
                    @php
                        $submissions = $userSubmissions->get($prompt->id, collect());
                        $latestSub = $submissions->first();
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs hover:shadow-md hover:border-indigo-300 transition flex flex-col justify-between overflow-hidden group">
                        
                        <div class="p-6 space-y-4">
                            
                            <!-- Badges -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->task_type->badgeClass() }}">
                                    {{ $prompt->task_type->label() }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $prompt->level->badgeClass() }}">
                                    {{ $prompt->level->shortLabel() }}
                                </span>
                            </div>

                            <!-- Chart thumbnail for Task 1 -->
                            @if ($prompt->image_path)
                                <div class="h-36 rounded-2xl overflow-hidden bg-slate-50 border border-slate-200">
                                    <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                            @endif

                            <!-- Title & Prompt Snippet -->
                            <div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                    {{ $prompt->prompt_type->label() }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition leading-snug">
                                    <a href="{{ route('student.writing.show', $prompt) }}">
                                        {{ $prompt->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-3 mt-2 leading-relaxed">
                                    {{ $prompt->prompt_text }}
                                </p>
                            </div>

                            <!-- Meta info -->
                            <div class="flex items-center gap-3 text-xs text-slate-500 pt-2 border-t border-slate-100">
                                <span>⏱ <strong>{{ $prompt->time_limit_minutes }}</strong> phút</span>
                                <span>•</span>
                                <span>≥ <strong>{{ $prompt->min_words }}</strong> từ</span>
                                @if ($prompt->sampleEssays->count() > 0)
                                    <span>•</span>
                                    <span class="text-indigo-600 font-semibold">{{ $prompt->sampleEssays->count() }} bài mẫu</span>
                                @endif
                            </div>

                            <!-- User past submission status badge -->
                            <div class="pt-1">
                                @if ($latestSub)
                                    @if ($latestSub->isGraded())
                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                                            <span class="font-bold text-emerald-800">Đã chấm điểm:</span>
                                            <span class="font-mono font-black text-emerald-700 text-sm">Band {{ number_format($latestSub->overall_score, 1) }}</span>
                                        </div>
                                    @elseif ($latestSub->isDraft())
                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs">
                                            <span class="font-semibold text-slate-700">Đang viết dở (bản nháp):</span>
                                            <span class="font-mono font-bold text-slate-800">{{ $latestSub->word_count }} từ</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-semibold">
                                            <span>Đã nộp bài (Chờ chấm)</span>
                                            <span>{{ $latestSub->word_count }} từ</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-[11px] text-slate-400 font-medium py-1">
                                        Chưa có lượt nộp bài nào
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Card Footer CTA -->
                        <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('student.writing.show', $prompt) }}" class="min-h-[40px] flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition">
                                Dàn ý & Bài mẫu
                            </a>
                            <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[40px] flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm shadow-indigo-600/20 transition">
                                {{ $latestSub && $latestSub->isDraft() ? 'Viết tiếp ✍️' : 'Luyện viết ✍️' }}
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $prompts->links() }}
            </div>
        @endif

    </div>
</x-layouts.student>
