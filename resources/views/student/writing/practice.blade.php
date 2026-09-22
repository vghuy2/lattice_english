<x-layouts.student title="Phòng Luyện viết - {{ $prompt->title }}">
    <div class="space-y-4 -mt-2">

        <!-- Top Sticky Exam Bar -->
        <div class="sticky top-16 z-30 bg-white/95 backdrop-blur-md border border-slate-200 rounded-3xl p-3.5 sm:p-4 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            
            <!-- Left: Back & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ route('student.writing.show', $prompt) }}" class="min-h-[36px] px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1" onclick="return confirm('Bạn có chắc muốn thoát phòng viết? Đừng quên lưu nháp trước khi rời đi.')">
                    ← Thoát
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $prompt->task_type->badgeClass() }}">
                            {{ $prompt->task_type->label() }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium hidden md:inline">≥ {{ $prompt->min_words }} từ</span>
                    </div>
                    <h1 class="text-sm font-bold text-slate-900 line-clamp-1 max-w-sm sm:max-w-md">
                        {{ $prompt->title }}
                    </h1>
                </div>
            </div>

            <!-- Middle: Timer -->
            <div class="flex items-center justify-center gap-2 bg-slate-100 px-4 py-1.5 rounded-2xl">
                <span class="text-xs font-bold text-slate-500">⏱ Thời gian:</span>
                <span id="timer-display" class="font-mono font-black text-sm text-indigo-700 min-w-[50px] text-center">
                    {{ sprintf('%02d:00', $prompt->time_limit_minutes) }}
                </span>
                <button type="button" id="timer-toggle-btn" onclick="toggleTimer()" class="p-1 text-slate-500 hover:text-slate-800 text-xs font-bold" title="Tạm dừng / Tiếp tục">
                    ⏸
                </button>
            </div>

            <!-- Right: Auto-Save Status & Action Buttons -->
            <div class="flex items-center justify-end gap-2">
                <span id="save-status" class="text-[11px] text-slate-400 font-medium hidden lg:inline">
                    {{ $draft ? 'Đã tải bản nháp trước đó' : 'Tự động lưu nháp mỗi 30s' }}
                </span>

                <button
                    type="button"
                    onclick="triggerManualDraftSave()"
                    class="min-h-[36px] px-3.5 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
                >
                    💾 Lưu nháp
                </button>

                <button
                    type="button"
                    onclick="validateAndSubmit()"
                    class="min-h-[36px] px-5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm shadow-indigo-600/20 transition cursor-pointer"
                >
                    Nộp bài 🎯
                </button>
            </div>
        </div>

        <!-- Main Workspace Split Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            
            <!-- Left Column: Prompt, Chart & Scaffolding Helpers (5 Cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-4">
                    
                    <!-- Tab switcher for helper panel -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-2xl text-xs font-bold">
                        <button type="button" onclick="switchHelperTab('prompt')" id="tab-btn-prompt" class="flex-1 py-1.5 rounded-xl bg-white text-indigo-700 shadow-xs transition">
                            📌 Đề bài
                        </button>
                        <button type="button" onclick="switchHelperTab('outline')" id="tab-btn-outline" class="flex-1 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition">
                            📋 Dàn ý gợi ý
                        </button>
                        @if ($prompt->guidance)
                            <button type="button" onclick="switchHelperTab('guidance')" id="tab-btn-guidance" class="flex-1 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition">
                                💡 Chiến lược
                            </button>
                        @endif
                    </div>

                    <!-- Panel 1: Prompt & Chart Image -->
                    <div id="panel-prompt" class="space-y-4">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-900 leading-relaxed font-sans space-y-2">
                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/70">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Đề bài Tiếng Anh:</span>
                                <button
                                    type="button"
                                    id="btn-student-speak-prompt"
                                    onclick="toggleStudentSpeakPrompt()"
                                    class="min-h-[28px] inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition cursor-pointer"
                                    title="Nghe phát âm đề bài tiếng Anh chuẩn"
                                >
                                    <span id="student-speak-icon">🔊</span>
                                    <span id="student-speak-text">Nghe đọc đề</span>
                                </button>
                            </div>
                            <p id="student-prompt-text" class="text-sm font-semibold text-slate-900 leading-relaxed">
                                {{ $prompt->prompt_text }}
                            </p>
                        </div>

                        @if ($prompt->image_path)
                            <div class="space-y-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Biểu đồ số liệu:</span>
                                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 p-2 cursor-pointer group" onclick="document.getElementById('chart-zoom-modal').classList.remove('hidden')">
                                    <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-full h-auto object-contain rounded-xl group-hover:opacity-90 transition">
                                    <span class="text-[10px] text-center text-indigo-600 font-semibold block mt-1">🔍 Nhấn vào ảnh để phóng to</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Panel 2: Suggested Outline -->
                    <div id="panel-outline" class="hidden space-y-3">
                        @if (!empty($prompt->suggested_outline))
                            <div class="space-y-2">
                                @foreach ($prompt->suggested_outline as $idx => $sec)
                                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                                        <strong class="text-indigo-950 font-bold block">{{ $sec['section'] ?? "Đoạn " . ($idx + 1) }}</strong>
                                        <p class="text-slate-600 leading-relaxed">{{ $sec['hint'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 text-center py-4">Chưa có dàn ý gợi ý cho đề bài này.</p>
                        @endif
                    </div>

                    <!-- Panel 3: Guidance & Strategy -->
                    @if ($prompt->guidance)
                        <div id="panel-guidance" class="hidden space-y-2 p-3 bg-amber-50/80 rounded-2xl border border-amber-200 text-xs text-amber-950">
                            <strong class="font-bold text-amber-900 block mb-1">Mẹo làm bài Band 4.5 – 5.0+:</strong>
                            <p class="whitespace-pre-line leading-relaxed">{{ $prompt->guidance }}</p>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Right Column: Academic Essay Editor (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <form id="writing-form" method="POST" action="{{ route('student.writing.submit', $prompt) }}">
                    @csrf
                    <input type="hidden" name="time_spent_seconds" id="time_spent_seconds" value="{{ $draft?->time_spent_seconds ?? 0 }}">

                    <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
                        
                        <!-- Editor Toolbar / Stats Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-700">Khung soạn thảo bài viết:</span>
                            </div>

                            <!-- Live Word Counter Badge -->
                            <div class="flex items-center gap-2">
                                <span id="word-badge" class="px-3 py-1 rounded-full font-mono text-xs font-bold border bg-amber-50 text-amber-800 border-amber-200 transition">
                                    <span id="live-word-count">0</span> / {{ $prompt->min_words }} từ
                                </span>
                            </div>
                        </div>

                        <!-- Main Academic Textarea -->
                        <div>
                            <textarea
                                name="essay_content"
                                id="essay_editor"
                                rows="18"
                                placeholder="Bắt đầu viết bài tại đây (Ví dụ: The line chart illustrates... / In modern society...)"
                                class="w-full p-4 rounded-2xl border border-slate-200 text-sm leading-relaxed font-serif focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 resize-y"
                                oninput="onEditorInput()"
                            >{{ old('essay_content', $draft?->essay_content ?? '') }}</textarea>
                            @error('essay_content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Editor Footer / Paragraph counter -->
                        <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-3">
                                <span>Đoạn văn: <strong id="paragraph-count" class="text-slate-800 font-mono">0</strong></span>
                                <span>•</span>
                                <span id="status-indicator">Tự động lưu nháp</span>
                            </div>

                            <span class="text-[11px] text-slate-400">
                                Nhấn Enter 2 lần để ngắt đoạn rõ ràng
                            </span>
                        </div>

                    </div>
                </form>
            </div>

        </div>

    </div>

    <!-- Chart Zoom Lightbox Modal (for Task 1) -->
    @if ($prompt->image_path)
        <div id="chart-zoom-modal" class="hidden fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
            <div class="bg-white rounded-3xl p-4 max-w-4xl max-h-[90vh] overflow-auto shadow-2xl relative" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-700">Biểu đồ Task 1 (Phóng to)</span>
                    <button type="button" onclick="document.getElementById('chart-zoom-modal').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-slate-900 font-bold">✕</button>
                </div>
                <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-full h-auto object-contain rounded-2xl">
            </div>
        </div>
    @endif

    <script>
        // 1. Timer Logic
        const minWordsTarget = {{ $prompt->min_words }};
        const initialLimitMinutes = {{ $prompt->time_limit_minutes }};
        let totalSecondsRemaining = initialLimitMinutes * 60;
        let timeSpentSeconds = {{ $draft?->time_spent_seconds ?? 0 }};
        let timerRunning = true;
        let timerInterval = null;

        function startTimer() {
            timerInterval = setInterval(() => {
                if (timerRunning) {
                    timeSpentSeconds++;
                    document.getElementById('time_spent_seconds').value = timeSpentSeconds;

                    if (totalSecondsRemaining > 0) {
                        totalSecondsRemaining--;
                    }
                    updateTimerDisplay();
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(totalSecondsRemaining / 60);
            const seconds = totalSecondsRemaining % 60;
            const displayEl = document.getElementById('timer-display');
            displayEl.innerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (totalSecondsRemaining === 0) {
                displayEl.classList.remove('text-indigo-700');
                displayEl.classList.add('text-rose-600', 'animate-pulse');
            }
        }

        function toggleTimer() {
            timerRunning = !timerRunning;
            const btn = document.getElementById('timer-toggle-btn');
            btn.innerText = timerRunning ? '⏸' : '▶️';
            btn.title = timerRunning ? 'Tạm dừng' : 'Tiếp tục';
        }

        // 2. Word & Paragraph Counter Logic
        const localStorageDraftKey = `lattice_writing_draft_{{ $prompt->id }}`;

        function calculateStats(text) {
            const trimmed = text.trim();
            const words = trimmed ? trimmed.split(/\s+/).length : 0;
            const paragraphs = trimmed ? trimmed.split(/\n\s*\n/).filter(p => p.trim().length > 0).length : 0;
            return { words, paragraphs };
        }

        function onEditorInput() {
            const text = document.getElementById('essay_editor').value;
            const { words, paragraphs } = calculateStats(text);

            document.getElementById('live-word-count').innerText = words;
            document.getElementById('paragraph-count').innerText = paragraphs;

            const badge = document.getElementById('word-badge');
            if (words >= minWordsTarget) {
                badge.className = 'px-3 py-1 rounded-full font-mono text-xs font-bold border bg-emerald-50 text-emerald-800 border-emerald-300';
            } else {
                badge.className = 'px-3 py-1 rounded-full font-mono text-xs font-bold border bg-amber-50 text-amber-800 border-amber-200';
            }

            // LocalStorage Instant Offline Backup
            if (text && text.trim().length > 0) {
                try {
                    localStorage.setItem(localStorageDraftKey, JSON.stringify({
                        content: text,
                        savedAt: new Date().toISOString()
                    }));
                } catch (e) {
                    // Ignore localStorage quota errors
                }
            }
        }

        // 3. Tab Switcher
        function switchHelperTab(tabName) {
            ['prompt', 'outline', 'guidance'].forEach(name => {
                const panel = document.getElementById(`panel-${name}`);
                const btn = document.getElementById(`tab-btn-${name}`);
                if (panel && btn) {
                    const isActive = (name === tabName);
                    panel.classList.toggle('hidden', !isActive);
                    btn.className = isActive
                        ? 'flex-1 py-1.5 rounded-xl bg-white text-indigo-700 shadow-xs transition'
                        : 'flex-1 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition';
                }
            });
        }

        // 4. Auto-Save Draft
        let draftSaveTimeout = null;
        function triggerManualDraftSave() {
            saveDraft();
        }

        async function saveDraft() {
            const content = document.getElementById('essay_editor').value;
            const spent = document.getElementById('time_spent_seconds').value;
            const statusEl = document.getElementById('save-status');

            statusEl.innerText = 'Đang lưu nháp...';

            try {
                const res = await fetch("{{ route('student.writing.draft', $prompt) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        essay_content: content,
                        time_spent_seconds: spent
                    })
                });

                if (res.ok) {
                    const data = await res.json();
                    statusEl.innerText = `✓ Đã lưu nháp lúc ${data.updated_at} (sao lưu offline)`;
                } else {
                    statusEl.innerText = 'Lưu nháp máy chủ thất bại (đã giữ bản offline)';
                }
            } catch (err) {
                statusEl.innerText = 'Lỗi mạng: đã tự động sao lưu bản nháp offline.';
            }
        }

        // Auto-save interval (every 30 seconds)
        setInterval(() => {
            if (document.getElementById('essay_editor').value.trim().length > 10) {
                saveDraft();
            }
        }, 30000);

        // 5. Validation and Submit
        function validateAndSubmit() {
            const content = document.getElementById('essay_editor').value.trim();
            const { words } = calculateStats(content);

            if (words < 30) {
                alert('Bài viết quá ngắn. Vui lòng viết ít nhất 30 từ trước khi nộp bài.');
                return;
            }

            if (words < minWordsTarget) {
                const confirmUnder = confirm(`Bài viết của bạn hiện có ${words} từ, chưa đạt số từ tối thiểu (${minWordsTarget} từ). Theo luật chấm IELTS, bài viết thiếu từ sẽ bị trừ điểm Task Achievement. Bạn có chắc chắn muốn nộp bài?`);
                if (!confirmUnder) return;
            } else {
                const confirmSubmit = confirm('Bạn có chắc chắn muốn nộp bài viết này để gửi tới hệ thống chấm điểm?');
                if (!confirmSubmit) return;
            }

            // Clear local storage draft after successful submission action
            try {
                localStorage.removeItem(localStorageDraftKey);
            } catch (e) {}

            document.getElementById('writing-form').submit();
        }

        // Initialize & Restore from LocalStorage if needed
        window.addEventListener('DOMContentLoaded', () => {
            const editor = document.getElementById('essay_editor');
            try {
                const cached = localStorage.getItem(localStorageDraftKey);
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (parsed.content && (!editor.value || parsed.content.length > editor.value.length)) {
                        editor.value = parsed.content;
                        const statusEl = document.getElementById('save-status');
                        if (statusEl) statusEl.innerText = '✓ Đã khôi phục bản nháp gần nhất từ bộ nhớ máy';
                    }
                }
            } catch (e) {}

            onEditorInput();
            startTimer();
        });

        let isStudentSpeaking = false;
        function toggleStudentSpeakPrompt() {
            if (!('speechSynthesis' in window)) {
                alert('Trình duyệt của bạn không hỗ trợ tính năng đọc âm thanh (Web Speech API).');
                return;
            }

            const btn = document.getElementById('btn-student-speak-prompt');
            const icon = document.getElementById('student-speak-icon');
            const text = document.getElementById('student-speak-text');

            if (isStudentSpeaking) {
                window.speechSynthesis.cancel();
                isStudentSpeaking = false;
                icon.textContent = '🔊';
                text.textContent = 'Nghe đọc đề';
                btn.classList.remove('bg-rose-50', 'text-rose-700', 'border-rose-200');
                btn.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                return;
            }

            const promptText = document.getElementById('student-prompt-text')?.textContent.trim();
            if (!promptText) return;

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(promptText);
            utterance.lang = 'en-US';
            utterance.rate = 0.95;

            utterance.onstart = function() {
                isStudentSpeaking = true;
                icon.textContent = '⏹️';
                text.textContent = 'Dừng đọc';
                btn.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
                btn.classList.add('bg-rose-50', 'text-rose-700', 'border-rose-200');
            };

            utterance.onend = utterance.onerror = function() {
                isStudentSpeaking = false;
                icon.textContent = '🔊';
                text.textContent = 'Nghe đọc đề';
                btn.classList.remove('bg-rose-50', 'text-rose-700', 'border-rose-200');
                btn.classList.add('bg-indigo-50', 'text-indigo-700', 'border-indigo-200');
            };

            window.speechSynthesis.speak(utterance);
        }
    </script>
</x-layouts.student>
