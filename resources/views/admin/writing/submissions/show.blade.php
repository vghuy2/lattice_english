<x-layouts.admin title="Chi tiết Bài nộp #{{ $submission->id }}">
    <div class="space-y-6 max-w-6xl mx-auto" x-data="{ isEditing: false }">

        <!-- Top Breadcrumb & Actions Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.writing.submissions.index') }}" class="hover:text-indigo-400 transition">Quản lý bài nộp</a>
                    <span>/</span>
                    <span class="text-slate-200">Bài làm #{{ $submission->id }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Bài nộp của: {{ $submission->user?->name ?? 'Học viên' }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.writing.submissions.rescore', $submission) }}" onsubmit="return confirm('Chấm lại bài nộp này bằng bộ luật scoring engine hiện hành?')">
                    @csrf
                    <button type="submit" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                        <span>🔄 Chấm lại tự động (Zero AI)</span>
                    </button>
                </form>

                <button type="button" @click="isEditing = !isEditing" class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 font-bold text-xs border border-amber-500/30 transition cursor-pointer">
                    <span x-text="isEditing ? 'Đóng form sửa' : '✏️ Điều chỉnh điểm'"></span>
                </button>

                <a href="{{ route('admin.writing.submissions.index') }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-2xl border border-slate-700 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition">
                    ← Quay lại
                </a>
            </div>
        </div>

        <!-- Student & Prompt Meta Info Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Student Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white font-black text-lg flex items-center justify-center shrink-0">
                    {{ mb_substr($submission->user?->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Học viên</span>
                    <h3 class="text-base font-extrabold text-white leading-tight">{{ $submission->user?->name }}</h3>
                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                        <span class="font-mono text-slate-400">{{ $submission->user?->email }}</span>
                        <span>•</span>
                        <span>Mục tiêu: Band {{ number_format($submission->user?->target_band ?? 6.0, 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Prompt Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs flex items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Đề bài</span>
                    <h3 class="text-base font-extrabold text-white line-clamp-1">{{ $prompt?->title }}</h3>
                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $prompt?->task_type?->badgeClass() ?? 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                            {{ $prompt?->task_type?->label() }}
                        </span>
                        <span>•</span>
                        <span>{{ $submission->word_count }} từ</span>
                        <span>•</span>
                        <span>{{ $submission->time_spent_formatted }}</span>
                    </div>
                </div>

                <!-- Band Score -->
                <div class="text-right shrink-0">
                    @if ($submission->isGraded())
                        <span class="px-3.5 py-1.5 rounded-2xl text-base font-mono font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 block">
                            Band {{ number_format($submission->overall_score, 1) }}
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 block">
                            Chưa chấm
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Manual Score Override Form (Collapsible) -->
        <div x-show="isEditing" class="bg-slate-900 border border-amber-500/30 rounded-2xl p-6 shadow-xs space-y-4" style="display: none;">
            <div class="flex items-center justify-between pb-3 border-b border-amber-500/20">
                <h3 class="text-base font-bold text-amber-400">Điều chỉnh điểm số & Nhận xét thủ công</h3>
                <span class="text-xs text-amber-300/80">Dành cho Giáo viên / Giám khảo xem xét lại</span>
            </div>

            <form method="POST" action="{{ route('admin.writing.submissions.feedback', $submission) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Overall Band</label>
                        <input type="number" step="0.5" min="3.5" max="9.0" name="overall_score" value="{{ old('overall_score', $submission->overall_score) }}" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 font-mono font-bold text-sm bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">TA / TR</label>
                        <input type="number" step="0.5" min="3.5" max="9.0" name="ta_score" value="{{ old('ta_score', $submission->ta_score) }}" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 font-mono font-bold text-sm bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">CC</label>
                        <input type="number" step="0.5" min="3.5" max="9.0" name="cc_score" value="{{ old('cc_score', $submission->cc_score) }}" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 font-mono font-bold text-sm bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">LR</label>
                        <input type="number" step="0.5" min="3.5" max="9.0" name="lr_score" value="{{ old('lr_score', $submission->lr_score) }}" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 font-mono font-bold text-sm bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">GRA</label>
                        <input type="number" step="0.5" min="3.5" max="9.0" name="gra_score" value="{{ old('gra_score', $submission->gra_score) }}" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 font-mono font-bold text-sm bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1">Nhận xét chi tiết cho học viên:</label>
                    <textarea name="feedback_notes" rows="4" class="w-full p-3 rounded-xl border border-slate-700 text-xs bg-slate-800 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 leading-relaxed font-sans placeholder:text-slate-500">{{ old('feedback_notes', $submission->feedback_notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="isEditing = false" class="min-h-[40px] px-4 py-2 rounded-xl bg-slate-800 text-slate-300 border border-slate-700 font-bold text-xs hover:bg-slate-700 transition">
                        Hủy
                    </button>
                    <button type="submit" class="min-h-[40px] px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>

        <!-- 4-Criteria Diagnostic Cards (If Graded) -->
        @if ($submission->isGraded())
            @php $bk = $submission->scoring_breakdown; @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- TA / TR -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="font-extrabold text-sm text-white">{{ $prompt?->isTask1() ? 'Task Achievement' : 'Task Response' }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Band {{ number_format($submission->ta_score, 1) }}</span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        @if (!empty($bk['criteria']['ta']['bonuses']))
                            @foreach ($bk['criteria']['ta']['bonuses'] as $b)
                                <div class="text-emerald-400">✓ {{ $b }}</div>
                            @endforeach
                        @endif
                        @if (!empty($bk['criteria']['ta']['penalties']))
                            @foreach ($bk['criteria']['ta']['penalties'] as $p)
                                <div class="text-amber-400">⚠️ {{ $p }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- CC -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="font-extrabold text-sm text-white">Coherence & Cohesion</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Band {{ number_format($submission->cc_score, 1) }}</span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        @if (!empty($bk['criteria']['cc']['bonuses']))
                            @foreach ($bk['criteria']['cc']['bonuses'] as $b)
                                <div class="text-emerald-400">✓ {{ $b }}</div>
                            @endforeach
                        @endif
                        @if (!empty($bk['criteria']['cc']['penalties']))
                            @foreach ($bk['criteria']['cc']['penalties'] as $p)
                                <div class="text-amber-400">⚠️ {{ $p }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- LR -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="font-extrabold text-sm text-white">Lexical Resource</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Band {{ number_format($submission->lr_score, 1) }}</span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        @if (!empty($bk['criteria']['lr']['bonuses']))
                            @foreach ($bk['criteria']['lr']['bonuses'] as $b)
                                <div class="text-emerald-400">✓ {{ $b }}</div>
                            @endforeach
                        @endif
                        @if (!empty($bk['criteria']['lr']['penalties']))
                            @foreach ($bk['criteria']['lr']['penalties'] as $p)
                                <div class="text-amber-400">⚠️ {{ $p }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- GRA -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="font-extrabold text-sm text-white">Grammatical Range & Accuracy</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Band {{ number_format($submission->gra_score, 1) }}</span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        @if (!empty($bk['criteria']['gra']['bonuses']))
                            @foreach ($bk['criteria']['gra']['bonuses'] as $b)
                                <div class="text-emerald-400">✓ {{ $b }}</div>
                            @endforeach
                        @endif
                        @if (!empty($bk['criteria']['gra']['penalties']))
                            @foreach ($bk['criteria']['gra']['penalties'] as $p)
                                <div class="text-amber-400">⚠️ {{ $p }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        @endif

        <!-- Student Essay Text Body -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-base font-extrabold text-white">Toàn văn bài làm của học viên</h3>
                <span class="text-xs font-mono text-slate-400 font-bold">({{ $submission->word_count }} words)</span>
            </div>

            <div class="prose prose-invert max-w-none text-slate-200 leading-relaxed font-sans text-sm sm:text-base space-y-4 whitespace-pre-line bg-slate-950/60 p-6 rounded-2xl border border-slate-800">
{{ $submission->essay_content }}
            </div>
        </div>

        <!-- Prompt Original Context -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xs space-y-3">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Đề bài gốc:</h4>
            <p class="text-sm font-semibold text-slate-200 leading-relaxed bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                {{ $prompt?->prompt_text }}
            </p>
        </div>

    </div>
</x-layouts.admin>
