<x-layouts.student title="Thư viện Từ vựng IELTS">
    <div class="space-y-8">
        
        <!-- Header & Top Stats Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl shadow-indigo-950/10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-indigo-200 mb-3">
                    <span>📚 IELTS Vocabulary Hub</span>
                    <span>•</span>
                    <span>Học theo ngữ cảnh học thuật</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Thư viện Từ vựng IELTS</h1>
                <p class="text-indigo-200 text-sm mt-1 max-w-xl leading-relaxed">
                    Học từ vựng theo chủ đề, ghi nhớ kèm Collocations và áp dụng chuẩn xác vào bài thi IELTS Writing.
                </p>
            </div>

            <!-- Stats & Quick Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center min-w-[110px]">
                    <span class="text-[11px] font-medium text-indigo-200 block uppercase tracking-wider">Đã học</span>
                    <span class="text-xl font-bold text-white">{{ $totalWordsLearned }} <span class="text-xs font-normal text-indigo-200">từ</span></span>
                </div>

                <div class="bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 text-center min-w-[110px]">
                    <span class="text-[11px] font-medium text-indigo-200 block uppercase tracking-wider">Cần ôn hôm nay</span>
                    <span class="text-xl font-bold {{ $dueReviewCount > 0 ? 'text-amber-300' : 'text-white' }}">
                        {{ $dueReviewCount }} <span class="text-xs font-normal text-indigo-200">từ</span>
                    </span>
                </div>

                <a href="{{ route('student.vocabulary.review.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-xs font-bold text-slate-900 bg-amber-300 hover:bg-amber-200 transition shadow-sm">
                    <span>⚡ Sổ tay ôn tập</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('student.vocabulary.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm bài học hoặc chủ đề từ vựng..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600"
                    >
                </div>

                <!-- Topic Selector -->
                <div>
                    <select
                        name="topic_id"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >
                        <option value="">Tất cả chủ đề</option>
                        @foreach ($topics as $tp)
                            <option value="{{ $tp->id }}" {{ $selectedTopic == $tp->id ? 'selected' : '' }}>
                                {{ $tp->icon ?? '📁' }} {{ $tp->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Selector & Filter button -->
                <div class="flex items-center gap-2">
                    <select
                        name="level"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >
                        <option value="">Tất cả trình độ</option>
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->value }}" {{ $selectedLevel === $lv->value ? 'selected' : '' }}>
                                {{ $lv->shortLabel() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="min-h-[44px] px-5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition cursor-pointer shrink-0">
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Lessons Grid -->
        <div>
            @if ($lessons->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($lessons as $lesson)
                        @php
                            $progress = $progressMap[$lesson->id] ?? null;
                            $percentage = $progress ? $progress->progress_percentage : 0;
                            $status = $progress ? $progress->status : \App\Enums\LearningStatus::NOT_STARTED;
                        @endphp
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs hover:shadow-md hover:border-slate-300 transition flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Card Body -->
                            <div class="p-6">
                                <!-- Top Badges -->
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600">
                                        <span>{{ $lesson->topic?->icon ?? '📁' }}</span>
                                        <span>{{ $lesson->topic?->title }}</span>
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $lesson->level->badgeClass() }}">
                                        {{ $lesson->level->shortLabel() }}
                                    </span>
                                </div>

                                <!-- Title & Description -->
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition leading-snug mb-2">
                                    <a href="{{ route('student.vocabulary.lessons.show', $lesson) }}">
                                        {{ $lesson->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-4">
                                    {{ $lesson->description ?: 'Bộ từ vựng trọng tâm kèm ví dụ và cụm từ đắt giá cho bài thi IELTS.' }}
                                </p>

                                <!-- Meta info -->
                                <div class="flex items-center gap-3 text-xs text-slate-500 mb-4">
                                    <span class="flex items-center gap-1 font-semibold text-slate-700">
                                        📝 {{ $lesson->items_count }} từ vựng
                                    </span>
                                    <span>•</span>
                                    <span>⏱ {{ $lesson->estimated_minutes }} phút</span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold {{ $percentage == 100 ? 'text-emerald-600' : 'text-slate-600' }}">
                                            {{ $status->label() }}
                                        </span>
                                        <span class="font-bold text-slate-800">{{ $percentage }}%</span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $percentage == 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Action Buttons -->
                            <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-3">
                                <a href="{{ route('student.vocabulary.lessons.show', $lesson) }}" class="min-h-[44px] flex-1 inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-xs">
                                    {{ $percentage > 0 ? 'Tiếp tục học →' : 'Bắt đầu học →' }}
                                </a>

                                <form method="POST" action="{{ route('student.vocabulary.practice.start', $lesson) }}">
                                    @csrf
                                    <button type="submit" class="min-h-[44px] inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition cursor-pointer" title="Luyện tập trắc nghiệm bài này">
                                        ⚡ Luyện tập
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $lessons->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 p-16 text-center shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-3">
                        📚
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Không tìm thấy bài học phù hợp</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Hãy thử điều chỉnh lại bộ lọc chủ đề hoặc từ khóa tìm kiếm.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('student.vocabulary.index') }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition">
                            Xem tất cả bài học
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.student>
