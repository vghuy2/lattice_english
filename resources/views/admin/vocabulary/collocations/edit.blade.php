<x-layouts.admin title="Chỉnh sửa Collocation: {{ $collocation->phrase }}">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.vocabulary.collocations.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2">
                    ← Quay lại danh sách Collocations
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Chỉnh sửa Collocation: {{ $collocation->phrase }}</h1>
                <p class="text-sm text-slate-400 mt-1">Cập nhật cấu trúc, nghĩa và ví dụ minh họa</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('admin.vocabulary.collocations.update', $collocation) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Row 1: Phrase & Type -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="phrase" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Cụm Collocation (Phrase) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="phrase"
                            name="phrase"
                            value="{{ old('phrase', $collocation->phrase) }}"
                            required
                            placeholder="Ví dụ: play an indispensable role in"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-bold"
                        >
                        @error('phrase')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Cấu trúc Cụm từ <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="type"
                            name="type"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($types as $tp)
                                <option value="{{ $tp->value }}" {{ old('type', $collocation->type?->value) === $tp->value ? 'selected' : '' }}>
                                    {{ $tp->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Meaning -->
                <div>
                    <label for="meaning" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Ý nghĩa Tiếng Việt chuẩn xác <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="meaning"
                        name="meaning"
                        value="{{ old('meaning', $collocation->meaning) }}"
                        required
                        placeholder="Ví dụ: đóng vai trò quan trọng trong việc..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
                    >
                    @error('meaning')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row 2: Topic, Level, Status, Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="topic_id" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Chủ đề Từ vựng (Topic)
                        </label>
                        <select
                            id="topic_id"
                            name="topic_id"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">-- Dùng chung (General) --</option>
                            @foreach ($topics as $tp)
                                <option value="{{ $tp->id }}" {{ (string)old('topic_id', $collocation->topic_id) === (string)$tp->id ? 'selected' : '' }}>
                                    {{ $tp->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="level" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Mục tiêu Band điểm <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="level"
                            name="level"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->value }}" {{ old('level', $collocation->level?->value) === $lv->value ? 'selected' : '' }}>
                                    {{ $lv->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('level')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Trạng thái <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}" {{ old('status', $collocation->status?->value) === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Thứ tự ưu tiên
                        </label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old('sort_order', $collocation->sort_order) }}"
                            min="0"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                        @error('sort_order')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Examples & Translations -->
                <div class="space-y-4 pt-2 border-t border-slate-800">
                    <div>
                        <label for="example_sentence" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Câu ví dụ minh họa trong văn cảnh IELTS Writing
                        </label>
                        <textarea
                            id="example_sentence"
                            name="example_sentence"
                            rows="2"
                            placeholder="Ví dụ: Technological innovation plays an indispensable role..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ old('example_sentence', $collocation->example_sentence) }}</textarea>
                        @error('example_sentence')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="example_sentence_vi" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Bản dịch Tiếng Việt của câu ví dụ
                        </label>
                        <textarea
                            id="example_sentence_vi"
                            name="example_sentence_vi"
                            rows="2"
                            placeholder="Ví dụ: Đổi mới công nghệ đóng một vai trò không thể thiếu..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >{{ old('example_sentence_vi', $collocation->example_sentence_vi) }}</textarea>
                        @error('example_sentence_vi')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Writing Notes / Tips -->
                <div class="pt-2 border-t border-slate-800">
                    <label for="writing_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Lưu ý ứng dụng trong IELTS Writing (Gợi ý ngữ cảnh / Lỗi thường gặp)
                    </label>
                    <textarea
                        id="writing_notes"
                        name="writing_notes"
                        rows="2"
                        placeholder="Ví dụ: Chú ý theo sau là giới từ 'in' + V-ing..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >{{ old('writing_notes', $collocation->writing_notes) }}</textarea>
                    @error('writing_notes')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <a href="{{ route('admin.vocabulary.collocations.index') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                        Hủy
                    </a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm cursor-pointer">
                        Cập nhật Collocation
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-layouts.admin>
