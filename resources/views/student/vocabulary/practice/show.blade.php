<x-layouts.student title="Luyện tập Từ vựng">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Top Quiz Bar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block">
                        {{ $session->session_type === 'review_quiz' ? '⚡ Bài Ôn Tập Nhanh' : '📝 Luyện tập theo bài học' }}
                    </span>
                    <h1 class="text-xl font-bold text-slate-900 mt-0.5">
                        {{ $session->lesson?->title ?? 'Sổ tay Từ vựng Cần Ôn' }}
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 font-mono font-bold text-xs">
                        {{ $session->answers->count() }} câu hỏi
                    </span>
                </div>
            </div>

            <!-- Quiz info note -->
            <p class="text-xs text-slate-500">
                Hãy hoàn thành các câu hỏi bên dưới để củng cố khả năng nhận diện và sử dụng từ vựng trong IELTS Writing.
            </p>
        </div>

        <!-- Quiz Form -->
        <form method="POST" action="{{ route('student.vocabulary.practice.submit', $session) }}" class="space-y-6" onsubmit="return confirm('Bạn có chắc chắn muốn nộp bài để xem kết quả và giải thích?')">
            @csrf

            @foreach ($session->answers as $index => $answer)
                @php
                    $qData = $answer->question_data;
                    $item = $answer->vocabularyItem;
                @endphp
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                    
                    <!-- Question Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-50 text-indigo-700">
                            Câu {{ $index + 1 }} / {{ $session->answers->count() }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400">
                            {{ $answer->question_type->label() }}
                        </span>
                    </div>

                    <!-- Prompt -->
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                            {{ $qData['prompt'] ?? 'Chọn đáp án đúng:' }}
                        </h2>

                        @if (!empty($qData['sentence']))
                            <div class="mt-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 text-sm font-medium text-slate-800 leading-relaxed italic">
                                "{{ $qData['sentence'] }}"
                            </div>
                        @endif

                        @if (!empty($qData['word']))
                            <div class="mt-3 inline-block px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-xl">
                                <span class="text-lg font-bold text-indigo-900">{{ $qData['word'] }}</span>
                                @if (!empty($qData['ipa']))
                                    <span class="text-xs font-mono text-indigo-600 ml-2">{{ $qData['ipa'] }}</span>
                                @endif
                            </div>
                        @endif

                        @if (!empty($qData['clue']))
                            <p class="text-xs text-amber-700 font-semibold mt-2">
                                💡 {{ $qData['clue'] }}
                            </p>
                        @endif
                    </div>

                    <!-- Options / Input depending on Type -->
                    <div class="pt-2">
                        @if ($answer->question_type->value === 'fill_in_blank')
                            <!-- Text input for fill in the blank -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Nhập từ tiếng Anh chính xác vào ô dưới:
                                </label>
                                <input
                                    type="text"
                                    name="answers[{{ $answer->id }}]"
                                    placeholder="Nhập từ..."
                                    autocomplete="off"
                                    class="w-full min-h-[44px] px-4 py-2.5 rounded-xl border border-slate-300 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                                >
                            </div>
                        @else
                            <!-- Radio options for MCQ / Meaning select / Contextual -->
                            <div class="grid grid-cols-1 gap-2.5">
                                @foreach ($qData['options'] ?? [] as $optIndex => $option)
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/30 cursor-pointer transition select-none has-checked:border-indigo-600 has-checked:bg-indigo-50/70 has-checked:ring-1 has-checked:ring-indigo-600">
                                        <input
                                            type="radio"
                                            name="answers[{{ $answer->id }}]"
                                            value="{{ $option }}"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-600 cursor-pointer"
                                        >
                                        <span class="text-sm font-medium text-slate-800 leading-snug">
                                            {{ $option }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach

            <!-- Submit Button Bar -->
            <div class="sticky bottom-6 bg-white/95 backdrop-blur-md border border-slate-200 rounded-3xl p-4 shadow-xl flex items-center justify-between gap-4">
                <a href="{{ route('student.vocabulary.index') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition">
                    Hủy bài thi
                </a>

                <button
                    type="submit"
                    class="min-h-[44px] flex-1 max-w-xs flex items-center justify-center py-3 px-6 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-md shadow-indigo-600/25 cursor-pointer"
                >
                    Nộp bài & Chấm điểm 🎯
                </button>
            </div>
        </form>

    </div>
</x-layouts.student>
