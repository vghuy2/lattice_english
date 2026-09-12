<x-layouts.admin title="{{ $prompt->title }}">
    <div class="space-y-6 max-w-5xl mx-auto">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <a href="{{ route('admin.writing.prompts.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    ← Quay lại danh sách Đề bài
                </a>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->task_type->badgeClass() }}">
                        {{ $prompt->task_type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $prompt->prompt_type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->level->badgeClass() }}">
                        {{ $prompt->level->shortLabel() }}
                    </span>
                    <span class="text-xs text-slate-400">
                        ⏱ {{ $prompt->time_limit_minutes }} phút • ≥ {{ $prompt->min_words }} từ
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.writing.prompts.edit', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Sửa Đề bài
                </a>
                <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-xs font-bold transition shadow-sm cursor-pointer">
                    <span>+ Thêm Bài mẫu</span>
                </a>
            </div>
        </div>

        <!-- Prompt Content Overview -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h1 class="text-2xl font-extrabold text-slate-900 leading-snug">
                {{ $prompt->title }}
            </h1>

            <!-- Prompt English Text -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block mb-2">Đề bài Tiếng Anh:</span>
                <p class="text-sm sm:text-base font-semibold text-slate-900 leading-relaxed">
                    {{ $prompt->prompt_text }}
                </p>
            </div>

            <!-- Chart image if available -->
            @if ($prompt->image_path)
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Biểu đồ / Bản đồ đính kèm (Task 1):</span>
                    <div class="rounded-2xl border border-slate-200 overflow-hidden max-w-xl bg-slate-50 p-2">
                        <img src="{{ $prompt->image_url }}" alt="Chart image" class="w-full h-auto object-contain rounded-xl">
                    </div>
                </div>
            @endif

            <!-- Guidance & Outline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <!-- Guidance -->
                @if ($prompt->guidance)
                    <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-900 block">💡 Hướng dẫn & Chiến lược:</span>
                        <p class="text-xs text-amber-950 leading-relaxed whitespace-pre-line">{{ $prompt->guidance }}</p>
                    </div>
                @endif

                <!-- Suggested Outline -->
                @if (!empty($prompt->suggested_outline))
                    <div class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-900 block">📋 Dàn ý gợi ý triển khai:</span>
                        <div class="space-y-2">
                            @foreach ($prompt->suggested_outline as $sec)
                                <div class="bg-white p-3 rounded-xl border border-indigo-100 text-xs">
                                    <strong class="font-bold text-indigo-950 block">{{ $sec['section'] ?? '' }}</strong>
                                    <p class="text-slate-600 mt-0.5">{{ $sec['hint'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sample Essays Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        Danh sách Bài mẫu Chuẩn Band ({{ $prompt->sampleEssays->count() }})
                    </h2>
                    <p class="text-xs text-slate-500">Các bài mẫu từ band 5.0 đến band 8.0 kèm phân tích chi tiết từng tiêu chí chấm.</p>
                </div>

                <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm cursor-pointer">
                    + Thêm bài mẫu
                </a>
            </div>

            @if ($prompt->sampleEssays->isEmpty())
                <div class="bg-white border border-slate-200 rounded-3xl p-8 text-center shadow-xs">
                    <p class="text-sm font-semibold text-slate-700">Chưa có bài mẫu nào cho đề bài này.</p>
                    <p class="text-xs text-slate-400 mt-1">Hãy thêm bài mẫu band 5.0 và 6.5+ để học viên đối chiếu khi luyện viết.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-100 transition">
                            Thêm bài mẫu ngay
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($prompt->sampleEssays as $essay)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                            
                            <!-- Essay Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="px-3.5 py-1.5 rounded-2xl font-mono font-black text-base {{ $essay->band_score >= 7.0 ? 'bg-purple-100 text-purple-800' : ($essay->band_score >= 6.0 ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                        Band {{ number_format($essay->band_score, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base">{{ $essay->title }}</h3>
                                        <span class="text-xs text-slate-400">Tác giả: {{ $essay->author_type }} • {{ $essay->word_count }} từ</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.writing.prompts.samples.edit', [$prompt, $essay]) }}" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                        Sửa
                                    </a>
                                    <form method="POST" action="{{ route('admin.writing.prompts.samples.destroy', [$prompt, $essay]) }}" onsubmit="return confirm('Bạn có chắc muốn xóa bài mẫu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-[36px] px-3.5 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition cursor-pointer">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Essay Text -->
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-sm font-medium text-slate-800 leading-relaxed whitespace-pre-line font-serif">
                                {{ $essay->essay_text }}
                            </div>

                            <!-- Highlighted Vocabulary & Structures -->
                            @if (!empty($essay->highlighted_vocabulary) || !empty($essay->highlighted_structures))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                    @if (!empty($essay->highlighted_vocabulary))
                                        <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-2">
                                            <span class="text-xs font-bold text-indigo-900 block">✨ Từ vựng đắt giá (Lexical Resource):</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($essay->highlighted_vocabulary as $v)
                                                    <span class="px-2.5 py-1 rounded-xl bg-white border border-indigo-200 text-xs font-semibold text-indigo-800" title="{{ $v['meaning'] ?? '' }} ({{ $v['note'] ?? '' }})">
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
                                            <span class="text-xs font-bold text-slate-800 block">📐 Cấu trúc ngữ pháp hay (GRA):</span>
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
                                    <strong class="font-bold text-amber-900 block">🔍 Nhận xét & Phân tích tiêu chí:</strong>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $essay->analysis_notes }}</p>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
