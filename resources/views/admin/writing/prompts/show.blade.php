<x-layouts.admin title="{{ $prompt->title }}">
    <div class="space-y-6 max-w-5xl mx-auto">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
            <div>
                <a href="{{ route('admin.writing.prompts.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                    ← Quay lại danh sách Đề bài
                </a>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prompt->task_type->badgeClass() }}">
                        {{ $prompt->task_type->label() }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
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
                <a href="{{ route('admin.writing.prompts.edit', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-bold transition border border-slate-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Sửa Đề bài
                </a>
                <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 text-xs font-bold transition shadow-sm cursor-pointer">
                    <span>+ Thêm Bài mẫu</span>
                </a>
            </div>
        </div>

        <!-- Prompt Content Overview -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h1 class="text-2xl font-extrabold text-white leading-snug">
                {{ $prompt->title }}
            </h1>

            <!-- Prompt English Text -->
            <div class="p-5 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-400 block">Đề bài Tiếng Anh:</span>
                    <button
                        type="button"
                        id="btn-speak-prompt"
                        onclick="toggleSpeakPrompt()"
                        class="min-h-[32px] inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 text-xs font-bold transition cursor-pointer"
                    >
                        <span id="speak-icon">🔊</span>
                        <span id="speak-text">Nghe đọc đề</span>
                    </button>
                </div>
                <p id="prompt-english-text" class="text-sm sm:text-base font-semibold text-slate-100 leading-relaxed">
                    {{ $prompt->prompt_text }}
                </p>
            </div>

            <!-- Chart image if available -->
            @if ($prompt->image_path)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Biểu đồ / Sơ đồ đính kèm (Task 1):</span>
                        <button type="button" onclick="document.getElementById('zoom-modal').classList.remove('hidden')" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold underline">
                            Phóng to toàn màn hình
                        </button>
                    </div>
                    <div class="rounded-2xl border border-slate-700 overflow-hidden max-w-xl bg-slate-800/60 p-2 cursor-pointer group" onclick="document.getElementById('zoom-modal').classList.remove('hidden')">
                        <img src="{{ $prompt->image_url }}" alt="Chart image" class="w-full h-auto object-contain rounded-xl group-hover:opacity-90 transition">
                        <span class="text-[11px] text-center text-indigo-400 font-semibold block mt-1.5">🔍 Nhấn vào ảnh để phóng to chi tiết</span>
                    </div>
                </div>
            @endif

            <!-- Guidance & Outline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <!-- Guidance -->
                @if ($prompt->guidance)
                    <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400 block">💡 Hướng dẫn & Chiến lược:</span>
                        <p class="text-xs text-amber-200/90 leading-relaxed whitespace-pre-line">{{ $prompt->guidance }}</p>
                    </div>
                @endif

                <!-- Suggested Outline -->
                @if (!empty($prompt->suggested_outline))
                    <div class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-300 block">📋 Dàn ý gợi ý triển khai:</span>
                        <div class="space-y-2">
                            @foreach ($prompt->suggested_outline as $sec)
                                <div class="bg-slate-800/90 p-3 rounded-xl border border-slate-700 text-xs">
                                    <strong class="font-bold text-indigo-300 block">{{ $sec['section'] ?? '' }}</strong>
                                    <p class="text-slate-400 mt-0.5">{{ $sec['hint'] ?? '' }}</p>
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
                    <h2 class="text-xl font-bold text-white">
                        Danh sách Bài mẫu Chuẩn Band ({{ $prompt->sampleEssays->count() }})
                    </h2>
                    <p class="text-xs text-slate-400">Các bài mẫu từ band 5.0 đến band 8.0 kèm phân tích chi tiết từng tiêu chí chấm.</p>
                </div>

                <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="min-h-[40px] inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-sm cursor-pointer">
                    + Thêm bài mẫu
                </a>
            </div>

            @if ($prompt->sampleEssays->isEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center shadow-xs">
                    <p class="text-sm font-semibold text-slate-300">Chưa có bài mẫu nào cho đề bài này.</p>
                    <p class="text-xs text-slate-400 mt-1">Hãy thêm bài mẫu band 5.0 và 6.5+ để học viên đối chiếu khi luyện viết.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.writing.prompts.samples.create', $prompt) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-bold text-xs hover:bg-indigo-500/20 transition">
                            Thêm bài mẫu ngay
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($prompt->sampleEssays as $essay)
                        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                            
                            <!-- Essay Header -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="px-3.5 py-1.5 rounded-2xl font-mono font-black text-base border {{ $essay->band_score >= 7.0 ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : ($essay->band_score >= 6.0 ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20') }}">
                                        Band {{ number_format($essay->band_score, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-base">{{ $essay->title }}</h3>
                                        <span class="text-xs text-slate-400">Tác giả: {{ $essay->author_type }} • {{ $essay->word_count }} từ</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.writing.prompts.samples.edit', [$prompt, $essay]) }}" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition">
                                        Sửa
                                    </a>
                                    <form method="POST" action="{{ route('admin.writing.prompts.samples.destroy', [$prompt, $essay]) }}" onsubmit="return confirm('Bạn có chắc muốn xóa bài mẫu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-[36px] px-3.5 py-1.5 rounded-xl text-rose-400 hover:bg-rose-500/10 text-xs font-bold transition cursor-pointer">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Essay Text -->
                            <div class="p-5 bg-slate-800/60 rounded-2xl border border-slate-700/60 text-sm font-medium text-slate-100 leading-relaxed whitespace-pre-line font-serif">
                                {{ $essay->essay_text }}
                            </div>

                            <!-- Highlighted Vocabulary & Structures -->
                            @if (!empty($essay->highlighted_vocabulary) || !empty($essay->highlighted_structures))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                    @if (!empty($essay->highlighted_vocabulary))
                                        <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 space-y-2">
                                            <span class="text-xs font-bold text-indigo-300 block">✨ Từ vựng đắt giá (Lexical Resource):</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($essay->highlighted_vocabulary as $v)
                                                    <span class="px-2.5 py-1 rounded-xl bg-slate-800 border border-indigo-500/30 text-xs font-semibold text-indigo-300" title="{{ $v['meaning'] ?? '' }} ({{ $v['note'] ?? '' }})">
                                                        <strong>{{ $v['word'] }}</strong>
                                                        @if (!empty($v['meaning']))
                                                            <span class="text-slate-400 font-normal">: {{ $v['meaning'] }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($essay->highlighted_structures))
                                        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                                            <span class="text-xs font-bold text-slate-300 block">📐 Cấu trúc ngữ pháp hay (GRA):</span>
                                            <div class="space-y-1.5">
                                                @foreach ($essay->highlighted_structures as $s)
                                                    <div class="text-xs font-mono bg-slate-900 p-2 rounded-lg border border-slate-700 text-slate-200">
                                                        {{ $s['pattern'] }}
                                                        @if (!empty($s['note']))
                                                            <span class="block text-[11px] font-sans text-slate-400 mt-0.5">👉 {{ $s['note'] }}</span>
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
                                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200 space-y-1">
                                    <strong class="font-bold text-amber-300 block">🔍 Nhận xét & Phân tích tiêu chí:</strong>
                                    <p class="whitespace-pre-line leading-relaxed">{{ $essay->analysis_notes }}</p>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    @if ($prompt->image_path)
        <!-- Chart Zoom Lightbox Modal -->
        <div id="zoom-modal" class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-xs flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
            <div class="relative max-w-4xl max-h-[90vh] bg-slate-900 border border-slate-700 rounded-3xl p-4 overflow-hidden" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <span class="text-sm font-bold text-white truncate max-w-md">{{ $prompt->title }}</span>
                    <button type="button" onclick="document.getElementById('zoom-modal').classList.add('hidden')" class="p-1.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-bold transition cursor-pointer">✕ Đóng</button>
                </div>
                <div class="p-2 overflow-auto max-h-[75vh] flex items-center justify-center">
                    <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="max-w-full max-h-full object-contain rounded-xl">
                </div>
            </div>
        </div>
    @endif

    <script>
        let isSpeaking = false;
        function toggleSpeakPrompt() {
            if (!('speechSynthesis' in window)) {
                alert('Trình duyệt của bạn không hỗ trợ tính năng đọc âm thanh (Web Speech API).');
                return;
            }

            const btn = document.getElementById('btn-speak-prompt');
            const icon = document.getElementById('speak-icon');
            const text = document.getElementById('speak-text');

            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                icon.textContent = '🔊';
                text.textContent = 'Nghe đọc đề';
                btn.classList.remove('bg-rose-500/20', 'text-rose-400', 'border-rose-500/30');
                btn.classList.add('bg-indigo-500/10', 'text-indigo-400', 'border-indigo-500/20');
                return;
            }

            const promptText = document.getElementById('prompt-english-text').textContent.trim();
            if (!promptText) return;

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(promptText);
            utterance.lang = 'en-US';
            utterance.rate = 0.95;

            utterance.onstart = function() {
                isSpeaking = true;
                icon.textContent = '⏹️';
                text.textContent = 'Dừng đọc';
                btn.classList.remove('bg-indigo-500/10', 'text-indigo-400', 'border-indigo-500/20');
                btn.classList.add('bg-rose-500/20', 'text-rose-400', 'border-rose-500/30');
            };

            utterance.onend = utterance.onerror = function() {
                isSpeaking = false;
                icon.textContent = '🔊';
                text.textContent = 'Nghe đọc đề';
                btn.classList.remove('bg-rose-500/20', 'text-rose-400', 'border-rose-500/30');
                btn.classList.add('bg-indigo-500/10', 'text-indigo-400', 'border-indigo-500/20');
            };

            window.speechSynthesis.speak(utterance);
        }
    </script>
</x-layouts.admin>
