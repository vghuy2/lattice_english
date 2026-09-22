<x-layouts.student title="Sổ Tay Lỗi Sai (Error Notebook)">
    <div class="space-y-8">

        <!-- Header Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-950 via-slate-900 to-indigo-950 text-white p-6 sm:p-8 md:p-10 shadow-xl shadow-rose-950/10">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-rose-200">
                        <span>📝 Sổ tay lỗi sai & Điểm yếu cần khắc phục</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
                        Sổ Tay Lỗi Sai Cá Nhân
                    </h1>

                    <p class="text-rose-200 text-xs sm:text-sm leading-relaxed max-w-xl">
                        Tổng hợp các từ vựng thường trả lời sai trong bài Quiz và những nhận xét chi tiết từ hệ thống chấm điểm để bạn rút kinh nghiệm và bứt phá điểm số.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('student.vocabulary.review.index') }}"
                       class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white text-sm font-semibold transition inline-flex items-center gap-2">
                        <span>Ôn tập Spaced Repetition →</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Two Columns: Weak Words & Writing Feedback -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Column 1: Weak Vocabulary -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Từ Vựng Thường Nhầm Lẫn ({{ $weakWords->total() }})
                    </h2>
                </div>

                @if($weakWords->isEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">
                        <p class="text-3xl mb-2">🎉</p>
                        <p class="font-medium text-sm text-slate-700">Tuyệt vời! Bạn không có từ vựng nào bị cảnh báo sai.</p>
                        <p class="text-xs text-slate-500 mt-1">Hãy tiếp tục duy trì việc ôn tập hàng ngày.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($weakWords as $review)
                            @php $item = $review->vocabularyItem; @endphp
                            @if($item)
                                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs hover:border-slate-300 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-slate-900 text-base">{{ $item->word }}</h3>
                                                <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono">{{ $item->part_of_speech->short() }}</span>
                                                @if($item->ipa)
                                                    <span class="text-xs text-slate-400">/{{ $item->ipa }}/</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-indigo-900 font-medium mt-1">{{ $item->vietnamese_meaning }}</p>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                Sai {{ $review->incorrect_count }} lần
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-600 bg-slate-50/50 p-2.5 rounded-lg">
                                        <span class="font-semibold text-slate-700">Ví dụ chuẩn:</span> "{{ $item->example_sentence }}"
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="pt-2">
                            {{ $weakWords->links() }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Column 2: Writing Mistakes & Examiner Notes -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Nhận Xét Cần Rút Kinh Nghiệm Trong Bài Viết
                    </h2>
                </div>

                @if($writingErrors->isEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">
                        <p class="text-3xl mb-2">✍️</p>
                        <p class="font-medium text-sm text-slate-700">Chưa có bài viết nào được chấm điểm.</p>
                        <p class="text-xs text-slate-500 mt-1">Hãy làm bài trong Writing Lab để nhận phân tích lỗi chi tiết.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($writingErrors as $submission)
                            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-semibold text-slate-900 text-sm truncate max-w-[280px]">
                                        {{ $submission->prompt?->title }}
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Band {{ number_format($submission->overall_score, 1) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-4 gap-2 my-2 text-center text-xs">
                                    <div class="p-1.5 rounded bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[10px]">TA</span>
                                        <span class="font-bold text-slate-800">{{ number_format($submission->ta_score, 1) }}</span>
                                    </div>
                                    <div class="p-1.5 rounded bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[10px]">CC</span>
                                        <span class="font-bold text-slate-800">{{ number_format($submission->cc_score, 1) }}</span>
                                    </div>
                                    <div class="p-1.5 rounded bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[10px]">LR</span>
                                        <span class="font-bold text-slate-800">{{ number_format($submission->lr_score, 1) }}</span>
                                    </div>
                                    <div class="p-1.5 rounded bg-slate-50 border border-slate-100">
                                        <span class="text-slate-400 block text-[10px]">GRA</span>
                                        <span class="font-bold text-slate-800">{{ number_format($submission->gra_score, 1) }}</span>
                                    </div>
                                </div>

                                @if($submission->feedback_notes)
                                    <div class="mt-2 text-xs text-slate-700 bg-amber-50/60 border border-amber-100 p-2.5 rounded-lg leading-relaxed whitespace-pre-line">
                                        {{ $submission->feedback_notes }}
                                    </div>
                                @endif

                                <div class="mt-3 text-right">
                                    <a href="{{ route('student.writing.submissions.show', $submission) }}"
                                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        Xem toàn bộ bài làm & phân tích chi tiết →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-layouts.student>
