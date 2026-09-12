<x-layouts.student title="{{ $prompt->title }}">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('student.writing.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 mb-1.5">
                    ← Quay lại Thư viện Writing
                </a>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->task_type->badgeClass() }}">
                        {{ $prompt->task_type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $prompt->prompt_type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->level->badgeClass() }}">
                        {{ $prompt->level->shortLabel() }}
                    </span>
                    <span class="text-xs text-slate-500">• ⏱ {{ $prompt->time_limit_minutes }} phút • ≥ {{ $prompt->min_words }} từ</span>
                </div>
            </div>

            <!-- Start Practice CTA -->
            <div>
                <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 cursor-pointer">
                    <span>✍️ Vào phòng Luyện viết</span>
                </a>
            </div>
        </div>

        <!-- Prompt Content Box -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                {{ $prompt->title }}
            </h1>

            <!-- Prompt English Text -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-2">Đề bài Tiếng Anh (IELTS Official Prompt):</span>
                <p class="text-base font-semibold text-slate-900 leading-relaxed font-sans">
                    {{ $prompt->prompt_text }}
                </p>
            </div>

            <!-- Chart / Map Image for Task 1 -->
            @if ($prompt->image_path)
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Biểu đồ số liệu đề thi:</span>
                    <div class="rounded-3xl border border-slate-200 overflow-hidden bg-slate-50 p-3 max-w-2xl mx-auto shadow-inner">
                        <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-full h-auto object-contain rounded-2xl">
                    </div>
                </div>
            @endif

            <!-- Guidance & Suggested Outline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @if ($prompt->guidance)
                    <div class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-2">
                        <strong class="text-xs font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                            <span>💡</span> Hướng dẫn & Chiến lược làm bài:
                        </strong>
                        <p class="text-xs text-amber-950 leading-relaxed whitespace-pre-line">{{ $prompt->guidance }}</p>
                    </div>
                @endif

                @if (!empty($prompt->suggested_outline))
                    <div class="p-5 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-2.5">
                        <strong class="text-xs font-bold uppercase tracking-wider text-indigo-950 flex items-center gap-1.5">
                            <span>📋</span> Dàn ý gợi ý triển khai:
                        </strong>
                        <div class="space-y-2">
                            @foreach ($prompt->suggested_outline as $sec)
                                <div class="bg-white p-3 rounded-xl border border-indigo-100 text-xs">
                                    <span class="font-bold text-indigo-900 block">{{ $sec['section'] ?? '' }}</span>
                                    <p class="text-slate-600 mt-0.5">{{ $sec['hint'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sample Essays Section -->
        @if ($prompt->sampleEssays->count() > 0)
            <div class="space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        Bài mẫu Tham khảo Chuẩn Band Điểm
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Đọc và phân tích bài mẫu để học cách phát triển ý, liên kết câu và sử dụng từ vựng đắt giá.
                    </p>
                </div>

                <div class="space-y-6">
                    @foreach ($prompt->sampleEssays as $essay)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                            
                            <!-- Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-2xl font-mono font-black text-sm {{ $essay->band_score >= 7.0 ? 'bg-purple-100 text-purple-800' : ($essay->band_score >= 6.0 ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                        Band {{ number_format($essay->band_score, 1) }}
                                    </span>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $essay->title }}</h3>
                                        <span class="text-[11px] text-slate-400">Nguồn: {{ $essay->author_type }} • {{ $essay->word_count }} từ</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Essay Text -->
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-sm font-medium text-slate-800 leading-relaxed whitespace-pre-line font-serif">
                                {{ $essay->essay_text }}
                            </div>

                            <!-- Highlights -->
                            @if (!empty($essay->highlighted_vocabulary) || !empty($essay->highlighted_structures))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                    @if (!empty($essay->highlighted_vocabulary))
                                        <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-2">
                                            <span class="text-xs font-bold text-indigo-900 block">✨ Từ vựng đắt giá (Lexical Resource):</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($essay->highlighted_vocabulary as $v)
                                                    <span class="px-2.5 py-1 rounded-xl bg-white border border-indigo-200 text-xs font-semibold text-indigo-800">
                                                        <strong>{{ $v['word'] }}</strong>
                                                        @if (!empty($v['meaning']))
                                                            <span class="text-slate-500 font-normal">: {{ $v['meaning'] }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($essay->highlighted_structures))
                                        <div class="p-4 rounded-2xl bg-slate-100/70 border border-slate-200 space-y-2">
                                            <span class="text-xs font-bold text-slate-800 block">📐 Cấu trúc câu nổi bật (GRA):</span>
                                            <div class="space-y-1.5">
                                                @foreach ($essay->highlighted_structures as $s)
                                                    <div class="text-xs font-mono bg-white p-2 rounded-lg border border-slate-200 text-slate-800">
                                                        {{ $s['pattern'] }}
                                                        @if (!empty($s['note']))
                                                            <span class="block text-[11px] font-sans text-slate-500 mt-0.5">👉 {{ $s['note'] }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Analysis Notes -->
                            @if ($essay->analysis_notes)
                                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-1">
                                    <strong class="font-bold text-amber-900 block">🔍 Nhận xét & Đánh giá theo tiêu chí:</strong>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $essay->analysis_notes }}</p>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bottom CTA Bar -->
        <div class="sticky bottom-6 bg-white/95 backdrop-blur-md border border-slate-200 rounded-3xl p-5 shadow-xl flex items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-slate-500 block">Sẵn sàng thực hành?</span>
                <span class="text-sm font-extrabold text-slate-900">Viết bài với đồng hồ bấm giờ và đếm từ tự động</span>
            </div>

            <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 cursor-pointer">
                <span>Vào phòng viết ngay ✍️</span>
            </a>
        </div>

    </div>
</x-layouts.student>
