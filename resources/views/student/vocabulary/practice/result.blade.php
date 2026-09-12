<x-layouts.student title="Kết quả Luyện tập">
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Result Overview Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs text-center relative overflow-hidden">
            <!-- Background accent glow -->
            <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full {{ $session->accuracy_rate >= 80 ? 'bg-emerald-100/50' : ($session->accuracy_rate >= 50 ? 'bg-amber-100/50' : 'bg-rose-100/50') }} blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block">
                    {{ $session->session_type === 'review_quiz' ? '⚡ Kết quả Ôn tập Nhanh' : '📝 Kết quả Luyện tập' }}
                </span>
                
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                    {{ $session->lesson?->title ?? 'Sổ tay Từ vựng Cần Ôn' }}
                </h1>

                <!-- Score circle / badge -->
                <div class="py-4">
                    <div class="inline-flex flex-col items-center justify-center w-32 h-32 rounded-full border-4 {{ $session->accuracy_rate >= 80 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : ($session->accuracy_rate >= 50 ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-rose-500 bg-rose-50 text-rose-700') }} shadow-inner">
                        <span class="text-3xl font-black font-mono">{{ $session->accuracy_rate }}%</span>
                        <span class="text-xs font-bold mt-0.5">
                            {{ $session->score }} / {{ $session->total_questions }} câu đúng
                        </span>
                    </div>
                </div>

                <!-- Evaluative Message -->
                <p class="text-sm font-medium text-slate-600 max-w-md mx-auto">
                    @if ($session->accuracy_rate >= 80)
                        🎉 Tuyệt vời! Bạn đã nắm rất vững các từ vựng này. Hãy áp dụng ngay vào bài Writing nhé!
                    @elseif ($session->accuracy_rate >= 50)
                        👍 Khá tốt! Bạn đã nhớ được phần lớn từ vựng. Hãy xem lại các câu sai bên dưới để ghi nhớ sâu hơn.
                    @else
                        💪 Đừng nản lòng! Hãy ôn lại các từ vựng này trong chế độ Flashcard và thử làm lại bài luyện tập.
                    @endif
                </p>

                <!-- Navigation buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    @if ($session->lesson)
                        <a href="{{ route('student.vocabulary.lessons.show', $session->lesson) }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-100 transition">
                            📖 Quay lại Bài học
                        </a>
                        <form method="POST" action="{{ route('student.vocabulary.practice.start', $session->lesson) }}">
                            @csrf
                            <button type="submit" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition shadow-sm shadow-indigo-600/20 cursor-pointer">
                                🔄 Luyện tập lại
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('student.vocabulary.review.index') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">
                        ⭐ Sổ tay & Ôn tập
                    </a>
                </div>
            </div>
        </div>

        <!-- Detailed Question Review -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900 px-1">
                Chi tiết câu trả lời và Giải thích Từ vựng
            </h2>

            @foreach ($session->answers as $index => $answer)
                @php
                    $qData = $answer->question_data;
                    $item = $answer->vocabularyItem;
                    $isCorrect = (bool) $answer->is_correct;
                @endphp
                <div class="bg-white border {{ $isCorrect ? 'border-emerald-200' : 'border-rose-200' }} rounded-3xl p-6 sm:p-7 shadow-xs space-y-4">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold {{ $isCorrect ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                Câu {{ $index + 1 }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">
                                {{ $answer->question_type->label() }}
                            </span>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            @if ($isCorrect)
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Đúng (+1)
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                Chưa chính xác
                            @endif
                        </span>
                    </div>

                    <!-- Question Prompt -->
                    <div>
                        <p class="text-sm sm:text-base font-bold text-slate-900">
                            {{ $qData['prompt'] ?? '' }}
                        </p>

                        @if (!empty($qData['sentence']))
                            <div class="mt-2.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-sm font-medium text-slate-800 italic">
                                "{{ $qData['sentence'] }}"
                            </div>
                        @endif

                        @if (!empty($qData['word']))
                            <div class="mt-2.5 inline-block px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-xl">
                                <span class="text-base font-bold text-indigo-900">{{ $qData['word'] }}</span>
                                @if (!empty($qData['ipa']))
                                    <span class="text-xs font-mono text-indigo-600 ml-2">{{ $qData['ipa'] }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Answers Comparison -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl {{ $isCorrect ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-rose-50/70 border border-rose-200' }}">
                            <span class="text-xs font-semibold text-slate-500 block mb-1">Câu trả lời của bạn:</span>
                            <span class="text-sm font-bold {{ $isCorrect ? 'text-emerald-900' : 'text-rose-900' }}">
                                {{ $answer->user_answer ?: '(Chưa điền đáp án)' }}
                            </span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-200">
                            <span class="text-xs font-semibold text-slate-500 block mb-1">Đáp án chính xác:</span>
                            <span class="text-sm font-bold text-indigo-900">
                                {{ $answer->correct_answer }}
                            </span>
                        </div>
                    </div>

                    <!-- Vocabulary Item Explanation & IELTS Notes -->
                    @if ($item)
                        <div class="pt-3 border-t border-slate-100 space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-slate-900">{{ $item->word }}</span>
                                @if ($item->ipa)
                                    <span class="text-xs font-mono text-slate-500">{{ $item->ipa }}</span>
                                @endif
                                <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                    {{ $item->part_of_speech->label() }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-700">
                                <span class="font-bold text-slate-900">Nghĩa:</span> {{ $item->meaning_vi }}
                            </p>

                            @if (!empty($item->collocations))
                                <div class="text-xs text-slate-700">
                                    <span class="font-bold text-indigo-700">Collocations:</span>
                                    <div class="inline-flex flex-wrap gap-1.5 ml-1">
                                        @foreach ((array)$item->collocations as $colloc)
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-800 font-semibold">{{ $colloc }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($item->ielts_usage_note)
                                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                                    <span class="font-bold">💡 Lưu ý Writing:</span> {{ $item->ielts_usage_note }}
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>
</x-layouts.student>
