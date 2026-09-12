<x-layouts.student title="{{ $lesson->title }}">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('student.vocabulary.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 mb-1.5">
                    ← Quay lại Thư viện từ vựng
                </a>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                        {{ $lesson->topic?->icon ?? '📁' }} {{ $lesson->topic?->title }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $lesson->level->badgeClass() }}">
                        {{ $lesson->level->shortLabel() }}
                    </span>
                    <span class="text-xs text-slate-500">• ⏱ {{ $lesson->estimated_minutes }} phút</span>
                </div>
            </div>

            <!-- View Mode Switch & Practice CTA -->
            <div class="flex items-center gap-2.5">
                <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs">
                    <a href="{{ route('student.vocabulary.lessons.show', [$lesson, 'mode' => 'cards']) }}"
                       class="min-h-[36px] px-3 py-1.5 rounded-lg font-semibold transition {{ $mode === 'cards' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        📇 Flashcard
                    </a>
                    <a href="{{ route('student.vocabulary.lessons.show', [$lesson, 'mode' => 'list']) }}"
                       class="min-h-[36px] px-3 py-1.5 rounded-lg font-semibold transition {{ $mode === 'list' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        📋 Danh sách
                    </a>
                </div>

                <form method="POST" action="{{ route('student.vocabulary.practice.start', $lesson) }}">
                    @csrf
                    <button type="submit" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                        ⚡ Luyện tập ngay
                    </button>
                </form>
            </div>
        </div>

        <!-- Lesson Header -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $lesson->title }}
            </h1>
            @if ($lesson->description)
                <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                    {{ $lesson->description }}
                </p>
            @endif

            <!-- Progress in Lesson -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700">Tiến độ bài học:</span>
                    <span class="px-2 py-0.5 rounded-md font-bold {{ $progress->progress_percentage == 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                        {{ $progress->completed_items_count }} / {{ $lesson->items->count() }} từ ({{ $progress->progress_percentage }}%)
                    </span>
                </div>
                <span class="text-slate-400">Đánh dấu trạng thái để lưu tiến độ tự động</span>
            </div>
        </div>

        <!-- Mode 1: Flashcard / Interactive Card Carousel -->
        @if ($mode === 'cards')
            <div id="flashcard-container" class="space-y-6">
                @foreach ($lesson->items as $index => $item)
                    @php
                        $review = $reviewsMap[$item->id] ?? null;
                        $currentStatus = $review ? $review->status->value : 'learning';
                        $isFav = $review ? $review->is_favorite : false;
                        $note = $review ? $review->personal_note : '';
                    @endphp
                    <div id="card-{{ $index }}" class="flashcard-slide {{ $index === 0 ? 'block' : 'hidden' }} bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-10 transition-all">
                        
                        <!-- Slide Header -->
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-50 text-indigo-700">
                                    Từ {{ $index + 1 }} / {{ $lesson->items->count() }}
                                </span>
                                @if ($review && $review->mastery_level > 0)
                                    <span class="text-xs text-amber-500 font-semibold" title="Mức độ ghi nhớ">
                                        {{ str_repeat('⭐', $review->mastery_level) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Favorite Star Button -->
                            <form method="POST" action="{{ route('student.vocabulary.items.favorite', $item) }}" class="inline">
                                @csrf
                                <button type="submit" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2 rounded-xl transition cursor-pointer {{ $isFav ? 'text-amber-500 bg-amber-50 hover:bg-amber-100' : 'text-slate-400 hover:text-amber-500 hover:bg-slate-100' }}" title="Lưu vào danh sách yêu thích">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Main Word Display -->
                        <div class="text-center py-4">
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                                    {{ $item->word }}
                                </h2>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $item->part_of_speech->short() }}
                                </span>
                            </div>

                            @if ($item->ipa)
                                <p class="text-sm font-mono text-indigo-600 font-semibold mt-1">
                                    {{ $item->ipa }}
                                </p>
                            @endif

                            @if ($item->audio_url)
                                <div class="mt-3 flex justify-center">
                                    <audio controls class="h-8 max-w-[240px]" src="{{ $item->audio_url }}"></audio>
                                </div>
                            @endif

                            <div class="mt-6 inline-block bg-emerald-50 border border-emerald-200/80 px-6 py-3 rounded-2xl">
                                <span class="text-xs uppercase font-bold text-emerald-700 block tracking-wider mb-0.5">Nghĩa Tiếng Việt</span>
                                <span class="text-lg font-bold text-emerald-900">{{ $item->vietnamese_meaning }}</span>
                            </div>
                        </div>

                        <!-- Example Sentence Box -->
                        <div class="mt-6 bg-slate-50 rounded-2xl p-5 border border-slate-200/80 text-left">
                            <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider block mb-1">
                                💡 Ngữ cảnh trong bài thi IELTS:
                            </span>
                            <p class="text-sm text-slate-800 font-medium leading-relaxed italic">
                                "{{ $item->example_sentence }}"
                            </p>
                            @if ($item->example_sentence_vi)
                                <p class="text-xs text-slate-500 mt-1.5">
                                    👉 {{ $item->example_sentence_vi }}
                                </p>
                            @endif
                        </div>

                        <!-- Collocations & Synonyms -->
                        @if (!empty($item->collocations) || !empty($item->synonyms))
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
                                @if (!empty($item->collocations))
                                    <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100">
                                        <span class="text-xs font-bold text-indigo-900 block mb-2">Collocations hay gặp:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($item->collocations as $colloc)
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white text-indigo-700 border border-indigo-200">
                                                    {{ $colloc }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($item->synonyms))
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                                        <span class="text-xs font-bold text-slate-800 block mb-2">Từ đồng nghĩa:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($item->synonyms as $syn)
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-slate-700 border border-slate-200">
                                                    {{ $syn }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Writing Tips -->
                        @if ($item->writing_notes)
                            <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-left text-xs text-amber-900">
                                <strong class="font-bold flex items-center gap-1.5 mb-1 text-amber-800">
                                    <span>✍️</span> Mẹo áp dụng trong IELTS Writing:
                                </strong>
                                <p class="leading-relaxed">{{ $item->writing_notes }}</p>
                            </div>
                        @endif

                        <!-- Mark Status Buttons -->
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <span class="text-xs font-semibold text-slate-500 block mb-3 text-center">Đánh giá mức độ ghi nhớ của bạn:</span>
                            <div class="grid grid-cols-3 gap-3">
                                <!-- Status: Review needed -->
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="review_needed">
                                    <button type="submit" class="w-full min-h-[44px] py-2.5 px-3 rounded-2xl text-xs font-bold transition cursor-pointer {{ $currentStatus === 'review_needed' ? 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-300' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                        ⚠️ Cần ôn lại
                                    </button>
                                </form>

                                <!-- Status: Learning -->
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="learning">
                                    <button type="submit" class="w-full min-h-[44px] py-2.5 px-3 rounded-2xl text-xs font-bold transition cursor-pointer {{ $currentStatus === 'learning' ? 'bg-amber-500 text-white shadow-sm ring-2 ring-amber-300' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                                        📖 Đang học
                                    </button>
                                </form>

                                <!-- Status: Known -->
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="known">
                                    <button type="submit" class="w-full min-h-[44px] py-2.5 px-3 rounded-2xl text-xs font-bold transition cursor-pointer {{ $currentStatus === 'known' ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-300' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        ✅ Đã thuộc từ
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Card Next / Prev Controls -->
                        <div class="mt-8 flex items-center justify-between">
                            <button type="button" onclick="switchCard({{ $index - 1 }})" {{ $index === 0 ? 'disabled' : '' }} class="min-h-[44px] px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                ← Từ trước
                            </button>

                            <button type="button" onclick="switchCard({{ $index + 1 }})" {{ $index === $lesson->items->count() - 1 ? 'disabled' : '' }} class="min-h-[44px] px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-xs">
                                Từ tiếp theo →
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>

            <script>
                function switchCard(targetIndex) {
                    const totalCards = {{ $lesson->items->count() }};
                    if (targetIndex < 0 || targetIndex >= totalCards) return;

                    document.querySelectorAll('.flashcard-slide').forEach((el, idx) => {
                        el.classList.toggle('hidden', idx !== targetIndex);
                        el.classList.toggle('block', idx === targetIndex);
                    });
                }
            </script>
        @else
            <!-- Mode 2: Detailed List View -->
            <div class="space-y-6">
                @foreach ($lesson->items as $index => $item)
                    @php
                        $review = $reviewsMap[$item->id] ?? null;
                        $currentStatus = $review ? $review->status->value : 'learning';
                        $isFav = $review ? $review->is_favorite : false;
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
                            <div class="flex items-baseline gap-3">
                                <span class="text-xs font-mono font-bold text-indigo-600">#{{ $index + 1 }}</span>
                                <h3 class="text-2xl font-bold text-slate-900">{{ $item->word }}</h3>
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $item->part_of_speech->short() }}
                                </span>
                                @if ($item->ipa)
                                    <span class="text-xs font-mono text-indigo-600 font-semibold">{{ $item->ipa }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($item->audio_url)
                                    <audio controls class="h-8 max-w-[180px]" src="{{ $item->audio_url }}"></audio>
                                @endif

                                <form method="POST" action="{{ route('student.vocabulary.items.favorite', $item) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="min-h-[44px] min-w-[44px] p-2 rounded-xl transition {{ $isFav ? 'text-amber-500 bg-amber-50' : 'text-slate-400 hover:text-amber-500 hover:bg-slate-100' }}">
                                        ★
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Meaning & Example -->
                        <div class="mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Nghĩa tiếng Việt:</span>
                            <p class="text-base font-bold text-emerald-800 mt-0.5">{{ $item->vietnamese_meaning }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-sm mb-4">
                            <p class="font-medium text-slate-800 italic">"{{ $item->example_sentence }}"</p>
                            @if ($item->example_sentence_vi)
                                <p class="text-xs text-slate-500 mt-1">👉 {{ $item->example_sentence_vi }}</p>
                            @endif
                        </div>

                        <!-- Quick status buttons in list -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="review_needed">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'review_needed' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                        ⚠️ Cần ôn lại
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="learning">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'learning' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                                        📖 Đang học
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('student.vocabulary.items.status', $item) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="known">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'known' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        ✅ Đã thuộc
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

        <!-- Bottom Fixed Practice CTA -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-indigo-950">Sẵn sàng kiểm tra mức độ ghi nhớ?</h3>
                <p class="text-xs text-indigo-700 mt-0.5">Luyện tập qua 5 dạng bài trắc nghiệm, điền từ và chọn theo ngữ cảnh IELTS.</p>
            </div>
            <form method="POST" action="{{ route('student.vocabulary.practice.start', $lesson) }}">
                @csrf
                <button type="submit" class="min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 cursor-pointer">
                    ⚡ Bắt đầu Luyện tập
                </button>
            </form>
        </div>

    </div>
</x-layouts.student>
