<x-layouts.admin title="Thêm Từ vựng Mới: {{ $lesson->title }}">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2">
                    ← Quay lại danh sách từ vựng của bài
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Thêm Từ vựng: {{ $lesson->title }}</h1>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('admin.vocabulary.lessons.items.store', $lesson) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">

                <!-- Row 1: Word, Part of Speech, IPA -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Word / Phrase -->
                    <div class="sm:col-span-1">
                        <label for="word" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Từ hoặc Cụm từ (Word / Phrase) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="word"
                            name="word"
                            value="{{ old('word') }}"
                            required
                            placeholder="Ví dụ: curriculum hoặc play a pivotal role"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold"
                        >
                    </div>

                    <!-- Part of Speech -->
                    <div>
                        <label for="part_of_speech" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Loại từ (Part of Speech) <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="part_of_speech"
                            name="part_of_speech"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($partsOfSpeech as $pos)
                                <option value="{{ $pos->value }}" {{ old('part_of_speech', 'noun') === $pos->value ? 'selected' : '' }}>
                                    {{ $pos->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- IPA -->
                    <div>
                        <label for="ipa" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Phiên âm IPA
                        </label>
                        <input
                            type="text"
                            id="ipa"
                            name="ipa"
                            value="{{ old('ipa') }}"
                            placeholder="/kəˈrɪk.jə.ləm/"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                    </div>
                </div>

                <!-- Vietnamese Meaning -->
                <div>
                    <label for="vietnamese_meaning" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Nghĩa Tiếng Việt chuẩn xác <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="vietnamese_meaning"
                        name="vietnamese_meaning"
                        value="{{ old('vietnamese_meaning') }}"
                        required
                        placeholder="Ví dụ: chương trình giảng dạy, khung chương trình học"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
                    >
                </div>

                <!-- Row 2: Example sentence & translation -->
                <div class="space-y-4">
                    <div>
                        <label for="example_sentence" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Câu ví dụ trong ngữ cảnh IELTS Writing <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="example_sentence"
                            name="example_sentence"
                            rows="2"
                            required
                            placeholder="Ví dụ: The school curriculum ought to incorporate practical vocational skills alongside theoretical knowledge."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ old('example_sentence') }}</textarea>
                    </div>

                    <div>
                        <label for="example_sentence_vi" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Dịch nghĩa câu ví dụ
                        </label>
                        <input
                            type="text"
                            id="example_sentence_vi"
                            name="example_sentence_vi"
                            value="{{ old('example_sentence_vi') }}"
                            placeholder="Ví dụ: Chương trình giảng dạy ở trường nên kết hợp các kỹ năng nghề thực tế bên cạnh kiến thức lý thuyết."
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <!-- Row 3: Collocations, Synonyms & Antonyms -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Collocations -->
                    <div>
                        <label for="collocations" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Collocations (cách nhau dấu phẩy hoặc xuống dòng)
                        </label>
                        <textarea
                            id="collocations"
                            name="collocations"
                            rows="3"
                            placeholder="core curriculum, national curriculum, reform the curriculum"
                            class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ is_array(old('collocations')) ? implode(', ', old('collocations')) : old('collocations') }}</textarea>
                    </div>

                    <!-- Synonyms -->
                    <div>
                        <label for="synonyms" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Từ đồng nghĩa (Synonyms)
                        </label>
                        <textarea
                            id="synonyms"
                            name="synonyms"
                            rows="3"
                            placeholder="syllabus, course of study, program"
                            class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ is_array(old('synonyms')) ? implode(', ', old('synonyms')) : old('synonyms') }}</textarea>
                    </div>

                    <!-- Antonyms -->
                    <div>
                        <label for="antonyms" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Từ trái nghĩa (Antonyms)
                        </label>
                        <textarea
                            id="antonyms"
                            name="antonyms"
                            rows="3"
                            placeholder="extracurricular activities (ngữ cảnh)"
                            class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ is_array(old('antonyms')) ? implode(', ', old('antonyms')) : old('antonyms') }}</textarea>
                    </div>
                </div>

                <!-- Writing Notes -->
                <div>
                    <label for="writing_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Ghi chú cách dùng trong IELTS Writing (Task 1 / Task 2)
                    </label>
                    <textarea
                        id="writing_notes"
                        name="writing_notes"
                        rows="2"
                        placeholder="Ví dụ: Rất thường dùng trong các đề Task 2 về Education khi bàn luận về môn học bắt buộc hay tự chọn. Tránh dùng sai số nhiều (curricula)."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >{{ old('writing_notes') }}</textarea>
                </div>

                <!-- Audio Upload & Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="audio" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            File âm thanh phát âm (MP3, WAV hoặc Link)
                        </label>
                        <input
                            type="file"
                            id="audio"
                            name="audio"
                            accept="audio/*"
                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-indigo-400 hover:file:bg-slate-700 cursor-pointer"
                        >
                    </div>

                    <div>
                        <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Thứ tự xuất hiện trong bài
                        </label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old('sort_order', $nextOrder) }}"
                            min="0"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="min-h-[44px] px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                        Hủy
                    </a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition cursor-pointer shadow-sm">
                        Lưu Từ vựng
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>
