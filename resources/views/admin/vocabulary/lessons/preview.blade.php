<x-layouts.admin title="Xem thử Bài học: {{ $lesson->title }}">
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Navigation Top -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
            <div>
                <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-1">
                    ← Quay lại danh sách bài học
                </a>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        Chế độ xem trước (Preview Mode)
                    </span>
                    <span class="text-xs text-slate-400">• {{ $lesson->items->count() }} từ vựng</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                    Quản lý Từ vựng
                </a>
                <a href="{{ route('admin.vocabulary.lessons.edit', $lesson) }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition">
                    Sửa bài học
                </a>
            </div>
        </div>

        <!-- Lesson Header Card (Student Style) -->
        <div class="bg-gradient-to-br from-indigo-900/40 via-slate-900 to-slate-900 border border-indigo-500/30 rounded-3xl p-6 sm:p-8 shadow-lg">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    {{ $lesson->topic?->icon ?? '📁' }} {{ $lesson->topic?->title }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $lesson->level->badgeClass() }}">
                    {{ $lesson->level->label() }}
                </span>
                <span class="text-xs text-slate-400">⏱ {{ $lesson->estimated_minutes }} phút học</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                {{ $lesson->title }}
            </h1>

            @if ($lesson->description)
                <p class="text-slate-300 text-sm mt-3 leading-relaxed max-w-3xl">
                    {{ $lesson->description }}
                </p>
            @endif
        </div>

        <!-- Words List Cards -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-white">Danh sách Từ vựng & Cụm từ Trong Bài ({{ $lesson->items->count() }})</h2>
                <span class="text-xs text-slate-400">Trình bày theo ngữ cảnh học thuật IELTS</span>
            </div>

            @if ($lesson->items->count() > 0)
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($lesson->items as $index => $item)
                        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs hover:border-slate-700 transition">
                            
                            <!-- Word Topbar -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-4 border-b border-slate-800">
                                <div class="flex items-baseline gap-3">
                                    <span class="text-xs font-mono font-bold text-indigo-400">#{{ $index + 1 }}</span>
                                    <h3 class="text-2xl font-bold text-white tracking-tight">{{ $item->word }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                        {{ $item->part_of_speech->short() }}
                                    </span>
                                    @if ($item->ipa)
                                        <span class="text-sm font-mono text-slate-400">{{ $item->ipa }}</span>
                                    @endif
                                </div>

                                @if ($item->audio_url)
                                    <div>
                                        <audio controls class="h-8 max-w-[200px]" src="{{ $item->audio_url }}"></audio>
                                    </div>
                                @endif
                            </div>

                            <!-- Vietnamese Meaning -->
                            <div class="mb-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ý nghĩa:</span>
                                <p class="text-base font-semibold text-emerald-400 mt-0.5">{{ $item->vietnamese_meaning }}</p>
                            </div>

                            <!-- Example Sentence in IELTS Context -->
                            <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-800 mb-4">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-400 block mb-1">
                                    💡 Ví dụ trong bài thi IELTS:
                                </span>
                                <p class="text-sm text-slate-200 font-medium leading-relaxed italic">
                                    "{{ $item->example_sentence }}"
                                </p>
                                @if ($item->example_sentence_vi)
                                    <p class="text-xs text-slate-400 mt-1">
                                        👉 {{ $item->example_sentence_vi }}
                                    </p>
                                @endif
                            </div>

                            <!-- Collocations, Synonyms & Antonyms Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <!-- Collocations -->
                                @if (!empty($item->collocations))
                                    <div class="space-y-1.5">
                                        <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">Collocations hay gặp:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($item->collocations as $colloc)
                                                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-medium">
                                                    {{ $colloc }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Synonyms & Antonyms -->
                                @if (!empty($item->synonyms) || !empty($item->antonyms))
                                    <div class="space-y-2">
                                        @if (!empty($item->synonyms))
                                            <div>
                                                <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">Từ đồng nghĩa (Synonyms):</span>
                                                <div class="flex flex-wrap gap-1.5 mt-1">
                                                    @foreach ($item->synonyms as $syn)
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                                            {{ $syn }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if (!empty($item->antonyms))
                                            <div>
                                                <span class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">Từ trái nghĩa (Antonyms):</span>
                                                <div class="flex flex-wrap gap-1.5 mt-1">
                                                    @foreach ($item->antonyms as $ant)
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-rose-500/10 text-rose-300 border border-rose-500/20">
                                                            {{ $ant }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Writing Usage Notes -->
                            @if ($item->writing_notes)
                                <div class="mt-4 pt-3 border-t border-slate-800 text-xs text-amber-300/90 flex items-start gap-2">
                                    <span class="text-sm">✍️</span>
                                    <div>
                                        <strong class="font-semibold text-amber-200">Ghi chú áp dụng trong IELTS Writing:</strong>
                                        <p class="mt-0.5 leading-relaxed text-slate-300">{{ $item->writing_notes }}</p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-12 text-center">
                    <p class="text-sm text-slate-400">Bài học này chưa có từ vựng nào.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.vocabulary.lessons.items.create', $lesson) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                            + Thêm từ vựng đầu tiên
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
