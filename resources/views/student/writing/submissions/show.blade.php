<x-layouts.student title="Chi tiết bài làm Writing">
    <div class="space-y-6 max-w-6xl mx-auto" x-data="{ activeTab: '{{ $submission->isGraded() ? 'analysis' : 'essay' }}' }">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('student.writing.index') }}" class="hover:text-indigo-600 transition">Luyện Writing</a>
                    <span>/</span>
                    <a href="{{ route('student.writing.submissions.index') }}" class="hover:text-indigo-600 transition">Sổ tay bài viết</a>
                    <span>/</span>
                    <span class="text-slate-800">Bài làm #{{ $submission->id }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $prompt->title }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                @if ($submission->isDraft())
                    <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                        <span>✏️ Viết tiếp bản nháp</span>
                    </a>
                @else
                    <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        <span>🔄 Luyện lại đề này</span>
                    </a>
                @endif
                <a href="{{ route('student.writing.submissions.index') }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition">
                    ← Danh sách
                </a>
            </div>
        </div>

        <!-- Overview Metric Badges -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Word Count Metric -->
            <div class="bg-white border border-slate-200 rounded-3xl p-4.5 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-lg">
                    ✍️
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Số lượng từ</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-lg font-mono font-black text-slate-900">{{ $submission->word_count }}</span>
                        <span class="text-xs font-semibold text-slate-400">/ {{ $prompt->min_words ?? ($prompt->task_type?->value === 'task_1' ? '150' : '250') }}+ từ</span>
                    </div>
                </div>
            </div>

            <!-- Time Spent -->
            <div class="bg-white border border-slate-200 rounded-3xl p-4.5 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 font-bold text-lg">
                    ⏱️
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Thời gian làm</span>
                    <span class="text-lg font-mono font-black text-slate-900">{{ $submission->time_spent_formatted }}</span>
                </div>
            </div>

            <!-- Task Type -->
            <div class="bg-white border border-slate-200 rounded-3xl p-4.5 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-lg">
                    📌
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Phân loại</span>
                    <span class="text-xs font-bold text-slate-800">{{ $prompt->task_type?->label() }}</span>
                </div>
            </div>

            <!-- Status / Score -->
            <div class="bg-white border border-slate-200 rounded-3xl p-4.5 shadow-xs flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl {{ $submission->isGraded() ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center font-bold text-lg">
                    🏆
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kết quả</span>
                    @if ($submission->isGraded())
                        <span class="text-lg font-mono font-black text-emerald-600">Band {{ number_format($submission->overall_score, 1) }}</span>
                    @elseif ($submission->isDraft())
                        <span class="text-xs font-bold text-slate-600">Bản nháp</span>
                    @else
                        <span class="text-xs font-bold text-amber-600">Đã nộp bài</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Graded Highlight Score Banner (If graded) -->
        @if ($submission->isGraded())
            <div class="bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-emerald-950/10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-xs font-semibold text-emerald-300 mb-2">
                            <span>🎯 Đã chấm điểm tự động theo chuẩn IELTS 4 tiêu chí</span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                            Band Score Tổng quan: <span class="font-mono text-emerald-400 font-black">Band {{ number_format($submission->overall_score, 1) }}</span>
                        </h3>
                        <p class="text-xs text-slate-300 mt-1 max-w-xl">
                            Được tính toán tự động bằng thuật toán làm tròn Band chính thức từ 4 tiêu chí: TA/TR, CC, LR, GRA.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="activeTab = 'analysis'" class="min-h-[44px] px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs transition shadow-md cursor-pointer">
                            Xem chi tiết từng tiêu chí ↓
                        </button>
                    </div>
                </div>

                <!-- 4 Criteria Sub-scores Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
                        <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">Task Achievement</span>
                        <span class="text-2xl font-mono font-black text-white mt-1 block">Band {{ number_format($submission->ta_score ?? 0, 1) }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
                        <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">Coherence & Cohesion</span>
                        <span class="text-2xl font-mono font-black text-white mt-1 block">Band {{ number_format($submission->cc_score ?? 0, 1) }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
                        <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">Lexical Resource</span>
                        <span class="text-2xl font-mono font-black text-white mt-1 block">Band {{ number_format($submission->lr_score ?? 0, 1) }}</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center">
                        <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider block">Grammatical Range</span>
                        <span class="text-2xl font-mono font-black text-white mt-1 block">Band {{ number_format($submission->gra_score ?? 0, 1) }}</span>
                    </div>
                </div>
            </div>
        @elseif (! $submission->isDraft())
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-5 shadow-xs flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-700 text-xl font-bold shrink-0">
                    ⏳
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Bài làm đã được ghi nhận</h4>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Bài viết của bạn đã được lưu thành công vào hệ thống.
                    </p>
                </div>
            </div>
        @endif

        <!-- Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-slate-200">
            @if ($submission->isGraded())
                <button type="button" @click="activeTab = 'analysis'" :class="activeTab === 'analysis' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                    <span>📊 Phân tích & Chấm điểm</span>
                </button>
            @endif
            <button type="button" @click="activeTab = 'essay'" :class="activeTab === 'essay' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                <span>📝 Bài làm của bạn</span>
            </button>
            <button type="button" @click="activeTab = 'prompt'" :class="activeTab === 'prompt' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                <span>📋 Đề bài & Dàn ý</span>
            </button>
            @if ($prompt->sampleEssays->isNotEmpty())
                <button type="button" @click="activeTab = 'samples'" :class="activeTab === 'samples' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-800 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition cursor-pointer flex items-center gap-2">
                    <span>🌟 Bài mẫu đối chiếu ({{ $prompt->sampleEssays->count() }})</span>
                </button>
            @endif
        </div>

        <!-- Tab: Analysis & Scoring Breakdown -->
        @if ($submission->isGraded())
            <div x-show="activeTab === 'analysis'" class="space-y-6">
                @php $bk = $submission->scoring_breakdown; @endphp

                <!-- 4 Criteria Detailed Assessment Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Criterion 1: TA / TR -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiêu chí 1</span>
                                <h4 class="text-base font-extrabold text-slate-900">{{ $prompt->isTask1() ? 'Task Achievement' : 'Task Response' }}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Band {{ number_format($submission->ta_score, 1) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            @if (!empty($bk['criteria']['ta']['bonuses']))
                                @foreach ($bk['criteria']['ta']['bonuses'] as $b)
                                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 flex items-start gap-2">
                                        <span class="font-bold">✓</span>
                                        <span>{{ $b }}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($bk['criteria']['ta']['penalties']))
                                @foreach ($bk['criteria']['ta']['penalties'] as $p)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-amber-900 flex items-start gap-2">
                                        <span class="font-bold">⚠️</span>
                                        <span>{{ $p }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Criterion 2: CC -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiêu chí 2</span>
                                <h4 class="text-base font-extrabold text-slate-900">Coherence & Cohesion</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Band {{ number_format($submission->cc_score, 1) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            @if (!empty($bk['criteria']['cc']['bonuses']))
                                @foreach ($bk['criteria']['cc']['bonuses'] as $b)
                                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 flex items-start gap-2">
                                        <span class="font-bold">✓</span>
                                        <span>{{ $b }}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($bk['criteria']['cc']['penalties']))
                                @foreach ($bk['criteria']['cc']['penalties'] as $p)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-amber-900 flex items-start gap-2">
                                        <span class="font-bold">⚠️</span>
                                        <span>{{ $p }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Criterion 3: LR -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiêu chí 3</span>
                                <h4 class="text-base font-extrabold text-slate-900">Lexical Resource</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Band {{ number_format($submission->lr_score, 1) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            @if (!empty($bk['criteria']['lr']['bonuses']))
                                @foreach ($bk['criteria']['lr']['bonuses'] as $b)
                                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 flex items-start gap-2">
                                        <span class="font-bold">✓</span>
                                        <span>{{ $b }}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($bk['criteria']['lr']['penalties']))
                                @foreach ($bk['criteria']['lr']['penalties'] as $p)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-amber-900 flex items-start gap-2">
                                        <span class="font-bold">⚠️</span>
                                        <span>{{ $p }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Criterion 4: GRA -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiêu chí 4</span>
                                <h4 class="text-base font-extrabold text-slate-900">Grammatical Range & Accuracy</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Band {{ number_format($submission->gra_score, 1) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs">
                            @if (!empty($bk['criteria']['gra']['bonuses']))
                                @foreach ($bk['criteria']['gra']['bonuses'] as $b)
                                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 flex items-start gap-2">
                                        <span class="font-bold">✓</span>
                                        <span>{{ $b }}</span>
                                    </div>
                                @endforeach
                            @endif

                            @if (!empty($bk['criteria']['gra']['penalties']))
                                @foreach ($bk['criteria']['gra']['penalties'] as $p)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-amber-900 flex items-start gap-2">
                                        <span class="font-bold">⚠️</span>
                                        <span>{{ $p }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Structured Full Feedback Report -->
                @if ($submission->feedback_notes)
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                        <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Nhận xét chi tiết & Lời khuyên nâng Band:</h4>
                        <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed font-sans whitespace-pre-line bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
{{ $submission->feedback_notes }}
                        </div>
                    </div>
                @endif

            </div>
        @endif

        <!-- Tab: Essay Content -->
        <div x-show="activeTab === 'essay'" class="space-y-4" style="{{ $submission->isGraded() ? 'display: none;' : '' }}">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nội dung bài viết</span>
                        <span class="text-xs text-slate-400 font-mono">({{ $submission->word_count }} words)</span>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">
                        Nộp lúc: {{ $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : $submission->updated_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                @if (empty(trim($submission->essay_content)))
                    <div class="py-12 text-center text-slate-400 text-sm">
                        Chưa có nội dung bài viết nào được ghi nhận.
                    </div>
                @else
                    <div class="prose prose-slate max-w-none text-slate-800 leading-relaxed font-sans text-sm sm:text-base space-y-4 whitespace-pre-line">
{{ $submission->essay_content }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab: Prompt Details & Strategy -->
        <div x-show="activeTab === 'prompt'" class="space-y-6" style="display: none;">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                <!-- Prompt Task Header -->
                <div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $prompt->task_type?->badgeClass() ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $prompt->task_type?->label() }}
                    </span>
                    <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                        {{ $prompt->title }}
                    </h2>
                </div>

                <!-- Prompt Instruction -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-sm sm:text-base font-semibold text-slate-800 leading-relaxed">
                    {{ $prompt->prompt_text }}
                </div>

                <!-- Chart Image (Task 1) -->
                @if ($prompt->image_path)
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Biểu đồ / Hình ảnh đề bài:</h4>
                        <div class="p-3 bg-slate-100 rounded-2xl border border-slate-200 overflow-hidden flex justify-center">
                            <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="max-h-96 object-contain rounded-xl">
                        </div>
                    </div>
                @endif

                <!-- Suggested Outline -->
                @if (!empty($prompt->suggested_outline))
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dàn ý gợi ý (Recommended Outline):</h4>
                        <div class="space-y-2">
                            @if (is_array($prompt->suggested_outline))
                                @foreach ($prompt->suggested_outline as $idx => $sec)
                                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                                        <strong class="text-indigo-950 font-bold block">{{ $sec['section'] ?? "Đoạn " . ($idx + 1) }}</strong>
                                        <p class="text-slate-600 leading-relaxed">{{ $sec['hint'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-mono">
                                    {{ $prompt->suggested_outline }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Guidance / Strategy -->
                @if ($prompt->guidance)
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Chiến lược làm bài (Guidance):</h4>
                        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 leading-relaxed whitespace-pre-line">
                            {{ $prompt->guidance }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab: Sample Essays -->
        @if ($prompt->sampleEssays->isNotEmpty())
            <div x-show="activeTab === 'samples'" class="space-y-6" style="display: none;">
                @foreach ($prompt->sampleEssays as $sample)
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Band {{ number_format($sample->band_score, 1) }}
                                </span>
                                <h3 class="font-extrabold text-base text-slate-900">{{ $sample->title }}</h3>
                            </div>
                            <span class="text-xs text-slate-400 font-mono">~ {{ $sample->word_count }} words</span>
                        </div>

                        <!-- Sample Essay Body -->
                        <div class="prose prose-slate max-w-none text-slate-800 leading-relaxed font-sans text-sm space-y-4 whitespace-pre-line bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
{{ $sample->essay_content }}
                        </div>

                        <!-- Examiner Commentary -->
                        @if ($sample->examiner_commentary)
                            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 space-y-1.5">
                                <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider block">Nhận xét của Giám khảo:</span>
                                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                    {{ $sample->examiner_commentary }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-layouts.student>
