<x-layouts.student title="Sổ tay Từ vựng & Ôn tập">
    <div class="space-y-6">

        <!-- Header & Stats Overview -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Sổ tay Từ vựng & Ôn tập
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Hệ thống ghi nhớ ngắt quãng (Spaced Repetition) giúp bạn ghi nhớ từ vựng IELTS lâu dài và tự tin dùng trong Writing.
                </p>
            </div>

            <!-- Quick Quiz Action -->
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('student.vocabulary.review.quiz') }}">
                    @csrf
                    <button
                        type="submit"
                        class="min-h-[44px] flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer"
                        @if ($dueCount === 0 && $totalStudiedCount === 0) disabled @endif
                    >
                        <span>⚡</span>
                        <span>Ôn tập Nhanh (10 từ)</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('student.vocabulary.review.index', ['tab' => 'due']) }}" class="p-5 rounded-3xl bg-white border {{ $tab === 'due' ? 'border-amber-400 ring-2 ring-amber-400/20 shadow-sm' : 'border-slate-200' }} hover:border-amber-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cần ôn hôm nay</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">⏰</span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-black text-amber-600 font-mono">{{ $dueCount }}</span>
                    <span class="text-xs text-slate-400 ml-1">từ</span>
                </div>
            </a>

            <a href="{{ route('student.vocabulary.review.index', ['tab' => 'favorites']) }}" class="p-5 rounded-3xl bg-white border {{ $tab === 'favorites' ? 'border-rose-400 ring-2 ring-rose-400/20 shadow-sm' : 'border-slate-200' }} hover:border-rose-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Từ vựng Yêu thích</span>
                    <span class="p-2 rounded-xl bg-rose-50 text-rose-600">⭐</span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-black text-rose-600 font-mono">{{ $favoritesCount }}</span>
                    <span class="text-xs text-slate-400 ml-1">từ</span>
                </div>
            </a>

            <a href="{{ route('student.vocabulary.review.index', ['tab' => 'all']) }}" class="p-5 rounded-3xl bg-white border {{ $tab === 'all' ? 'border-indigo-400 ring-2 ring-indigo-400/20 shadow-sm' : 'border-slate-200' }} hover:border-indigo-300 transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng từ đã học</span>
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">📚</span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-black text-indigo-600 font-mono">{{ $totalStudiedCount }}</span>
                    <span class="text-xs text-slate-400 ml-1">từ</span>
                </div>
            </a>
        </div>

        <!-- Filter and Search Bar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 shadow-xs">
            <form method="GET" action="{{ route('student.vocabulary.review.index') }}" class="flex flex-col md:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Tab switcher -->
                <div class="flex items-center bg-slate-100 p-1 rounded-2xl w-full md:w-auto">
                    <a href="{{ route('student.vocabulary.review.index', array_merge(request()->query(), ['tab' => 'due', 'page' => 1])) }}" class="flex-1 md:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ $tab === 'due' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Cần ôn ({{ $dueCount }})
                    </a>
                    <a href="{{ route('student.vocabulary.review.index', array_merge(request()->query(), ['tab' => 'favorites', 'page' => 1])) }}" class="flex-1 md:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ $tab === 'favorites' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Yêu thích ({{ $favoritesCount }})
                    </a>
                    <a href="{{ route('student.vocabulary.review.index', array_merge(request()->query(), ['tab' => 'all', 'page' => 1])) }}" class="flex-1 md:flex-none px-4 py-2 rounded-xl text-xs font-bold transition text-center {{ $tab === 'all' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tất cả ({{ $totalStudiedCount }})
                    </a>
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm từ vựng, phiên âm, nghĩa..."
                        class="w-full min-h-[44px] pl-10 pr-4 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Status Filter -->
                <select name="status" class="w-full md:w-44 min-h-[44px] px-3 rounded-2xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" @selected($selectedStatus === $st->value)>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="min-h-[44px] px-5 py-2.5 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition w-full md:w-auto">
                    Lọc
                </button>
            </form>
        </div>

        <!-- Words List Table / Cards -->
        @if ($reviews->isEmpty())
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 flex items-center justify-center text-2xl text-indigo-600 mb-3">
                    📖
                </div>
                <h3 class="text-base font-bold text-slate-900">Không tìm thấy từ vựng nào</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    @if ($tab === 'due')
                        Tuyệt vời! Bạn không còn từ nào cần ôn tập hôm nay. Hãy học thêm bài mới trong Thư viện!
                    @elseif ($tab === 'favorites')
                        Bạn chưa gắn sao yêu thích từ vựng nào. Hãy nhấn biểu tượng ⭐ khi học để lưu từ vào đây.
                    @else
                        Bạn chưa học từ vựng nào. Hãy bắt đầu chọn bài học trong Thư viện từ vựng!
                    @endif
                </p>
                <div class="mt-4">
                    <a href="{{ route('student.vocabulary.index') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition">
                        Khám phá Thư viện Từ vựng
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($reviews as $review)
                    @php $item = $review->vocabularyItem; @endphp
                    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs hover:border-indigo-300 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <!-- Word Info -->
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-lg font-bold text-slate-900">{{ $item->word }}</span>
                                @if ($item->ipa)
                                    <span class="text-xs font-mono text-slate-500">{{ $item->ipa }}</span>
                                @endif
                                @if ($item->part_of_speech)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                        {{ $item->part_of_speech->label() }}
                                    </span>
                                @endif
                                @if ($item->lesson?->level)
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700">
                                        {{ $item->lesson->level->shortLabel() }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs font-semibold text-slate-700">
                                {{ $item->vietnamese_meaning }}
                            </p>

                            @if (!empty($item->collocations))
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach (array_slice((array)$item->collocations, 0, 3) as $colloc)
                                        <span class="text-[11px] px-2 py-0.5 rounded bg-indigo-50/70 text-indigo-800 font-medium">
                                            {{ $colloc }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-3 pt-1 text-[11px] text-slate-400">
                                @if ($item->lesson)
                                    <span>Bài: <a href="{{ route('student.vocabulary.lessons.show', $item->lesson) }}" class="text-indigo-600 hover:underline font-semibold">{{ $item->lesson->title }}</a></span>
                                    <span>•</span>
                                @endif
                                <span>Luyện: <strong class="text-slate-700 font-mono">{{ $review->review_count }}</strong> lần</span>
                                <span>•</span>
                                <span>Đúng: <strong class="text-emerald-700 font-mono">{{ $review->correct_count }}</strong></span>
                            </div>
                        </div>

                        <!-- Mastery & Status Actions -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            <!-- Mastery level dots -->
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Mức ghi nhớ ({{ $review->mastery_level }}/5)</span>
                                <div class="flex items-center gap-1 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="w-3 h-3 rounded-full {{ $i <= $review->mastery_level ? 'bg-indigo-600' : 'bg-slate-200' }}"></div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Word status badge / toggle form -->
                            <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}" class="flex items-center gap-1">
                                @csrf
                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    class="min-h-[44px] px-3 rounded-xl border border-slate-200 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600 {{ $review->status->value === 'known' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($review->status->value === 'learning' ? 'bg-indigo-50 text-indigo-800 border-indigo-300' : 'bg-amber-50 text-amber-800 border-amber-300') }}"
                                >
                                    <option value="known" @selected($review->status->value === 'known')>Đã thuộc</option>
                                    <option value="learning" @selected($review->status->value === 'learning')>Đang học</option>
                                    <option value="review_needed" @selected($review->status->value === 'review_needed')>Cần ôn lại</option>
                                </select>
                            </form>

                            <!-- Favorite toggle -->
                            <form method="POST" action="{{ route('student.vocabulary.items.favorite', $item) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl border {{ $review->is_favorite ? 'border-amber-300 bg-amber-50 text-amber-500' : 'border-slate-200 text-slate-400 hover:text-amber-500' }} transition cursor-pointer"
                                    title="{{ $review->is_favorite ? 'Bỏ yêu thích' : 'Gắn sao yêu thích' }}"
                                >
                                    <span class="text-base">{{ $review->is_favorite ? '★' : '☆' }}</span>
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layouts.student>
