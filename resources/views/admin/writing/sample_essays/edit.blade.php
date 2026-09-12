<x-layouts.admin title="Chỉnh sửa Bài mẫu Writing">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Breadcrumb -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    ← Quay lại Đề bài: {{ $prompt->title }}
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Chỉnh sửa Bài mẫu Writing</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.writing.prompts.samples.update', [$prompt, $essay]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Essay Info & Score -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">
                    1. Thông tin bài mẫu & Điểm Band
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tiêu đề Bài mẫu <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $essay->title) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                        @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Band điểm chuẩn <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            step="0.5"
                            min="4.0"
                            max="9.0"
                            name="band_score"
                            value="{{ old('band_score', $essay->band_score) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                        @error('band_score') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nguồn / Tác giả bài viết <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="author_type"
                            value="{{ old('author_type', $essay->author_type) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Trạng thái
                        </label>
                        <select name="status" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold">
                            <option value="published" @selected(old('status', $essay->status) === 'published')>Công khai (Published)</option>
                            <option value="draft" @selected(old('status', $essay->status) === 'draft')>Bản nháp (Draft)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Thứ tự hiển thị
                        </label>
                        <input type="number" name="order_index" value="{{ old('order_index', $essay->order_index) }}" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 2: Essay Full Text & Live Counter -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">2. Toàn văn Bài mẫu</h2>
                    <span id="word-count-badge" class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-mono text-xs font-bold">
                        {{ $essay->word_count }} từ
                    </span>
                </div>

                <div>
                    <textarea
                        name="essay_text"
                        id="essay_text"
                        rows="12"
                        oninput="updateWordCount(this.value)"
                        class="w-full p-4 rounded-2xl border border-slate-300 text-sm leading-relaxed font-serif focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >{{ old('essay_text', $essay->essay_text) }}</textarea>
                    @error('essay_text') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nhận xét chi tiết theo 4 tiêu chí (TA/TR, CC, LR, GRA)
                    </label>
                    <textarea
                        name="analysis_notes"
                        rows="4"
                        class="w-full p-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >{{ old('analysis_notes', $essay->analysis_notes) }}</textarea>
                </div>
            </div>

            <!-- Section 3: Highlighted Vocabulary Annotations -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">3. Từ vựng & Collocations đắt giá (Lexical Resource)</h2>
                        <p class="text-xs text-slate-500">Ghi chú từ vựng hay trong bài mẫu để học viên học hỏi.</p>
                    </div>
                    <button type="button" onclick="addVocabRow()" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition cursor-pointer">
                        + Thêm từ vựng
                    </button>
                </div>

                <div id="vocab-rows" class="space-y-3">
                    @php $vocabs = old('highlighted_vocabulary', $essay->highlighted_vocabulary ?? []); @endphp
                    @forelse ($vocabs as $idx => $v)
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 vocab-row">
                            <div>
                                <input type="text" name="highlighted_vocabulary[{{ $idx }}][word]" value="{{ $v['word'] ?? '' }}" placeholder="Từ / Cụm từ" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            </div>
                            <div>
                                <input type="text" name="highlighted_vocabulary[{{ $idx }}][meaning]" value="{{ $v['meaning'] ?? '' }}" placeholder="Nghĩa TV" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                            </div>
                            <div>
                                <input type="text" name="highlighted_vocabulary[{{ $idx }}][band]" value="{{ $v['band'] ?? '' }}" placeholder="Band" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" name="highlighted_vocabulary[{{ $idx }}][note]" value="{{ $v['note'] ?? '' }}" placeholder="Lưu ý..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.vocab-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @empty
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 vocab-row">
                            <div>
                                <input type="text" name="highlighted_vocabulary[0][word]" placeholder="Từ / Cụm từ" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            </div>
                            <div>
                                <input type="text" name="highlighted_vocabulary[0][meaning]" placeholder="Nghĩa TV" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                            </div>
                            <div>
                                <input type="text" name="highlighted_vocabulary[0][band]" placeholder="Band" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" name="highlighted_vocabulary[0][note]" placeholder="Lưu ý..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.vocab-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 4: Highlighted Grammatical Structures -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">4. Cấu trúc câu ngữ pháp nổi bật (GRA)</h2>
                        <p class="text-xs text-slate-500">Mẫu câu phức, đảo ngữ, bị động hoặc so sánh phức tạp.</p>
                    </div>
                    <button type="button" onclick="addStructureRow()" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition cursor-pointer">
                        + Thêm cấu trúc
                    </button>
                </div>

                <div id="structure-rows" class="space-y-3">
                    @php $structs = old('highlighted_structures', $essay->highlighted_structures ?? []); @endphp
                    @forelse ($structs as $idx => $s)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 struct-row">
                            <div>
                                <input type="text" name="highlighted_structures[{{ $idx }}][pattern]" value="{{ $s['pattern'] ?? '' }}" placeholder="Cấu trúc" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold font-mono">
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="text" name="highlighted_structures[{{ $idx }}][note]" value="{{ $s['note'] ?? '' }}" placeholder="Ghi chú..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.struct-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @empty
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 struct-row">
                            <div>
                                <input type="text" name="highlighted_structures[0][pattern]" placeholder="Cấu trúc" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold font-mono">
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="text" name="highlighted_structures[0][note]" placeholder="Ghi chú..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.struct-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Submit bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="min-h-[44px] px-6 py-2.5 rounded-2xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition flex items-center">
                    Hủy bỏ
                </a>
                <button type="submit" class="min-h-[44px] px-8 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Cập nhật Bài mẫu →
                </button>
            </div>
        </form>

    </div>

    <script>
        function updateWordCount(text) {
            const count = text.trim() ? text.trim().split(/\s+/).length : 0;
            document.getElementById('word-count-badge').innerText = `${count} từ`;
        }

        let vocabIdx = {{ count($vocabs ?? []) + 10 }};
        function addVocabRow() {
            const container = document.getElementById('vocab-rows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-4 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 vocab-row';
            row.innerHTML = `
                <div>
                    <input type="text" name="highlighted_vocabulary[${vocabIdx}][word]" placeholder="Từ / Cụm từ" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                </div>
                <div>
                    <input type="text" name="highlighted_vocabulary[${vocabIdx}][meaning]" placeholder="Nghĩa TV" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div>
                    <input type="text" name="highlighted_vocabulary[${vocabIdx}][band]" placeholder="Band" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="highlighted_vocabulary[${vocabIdx}][note]" placeholder="Lưu ý..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.closest('.vocab-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                </div>
            `;
            container.appendChild(row);
            vocabIdx++;
        }

        let structIdx = {{ count($structs ?? []) + 10 }};
        function addStructureRow() {
            const container = document.getElementById('structure-rows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 struct-row';
            row.innerHTML = `
                <div>
                    <input type="text" name="highlighted_structures[${structIdx}][pattern]" placeholder="Cấu trúc" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold font-mono">
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="text" name="highlighted_structures[${structIdx}][note]" placeholder="Ghi chú..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.closest('.struct-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                </div>
            `;
            container.appendChild(row);
            structIdx++;
        }
    </script>
</x-layouts.admin>
