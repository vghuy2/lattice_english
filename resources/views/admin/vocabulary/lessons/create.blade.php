<x-layouts.admin title="Tạo Bài học Từ vựng Mới">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.vocabulary.lessons.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2">
                    ← Quay lại danh sách bài học
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Tạo Bài học Từ vựng Mới</h1>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('admin.vocabulary.lessons.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Topic Selector -->
                <div>
                    <label for="topic_id" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Thuộc Chủ đề <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="topic_id"
                        name="topic_id"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">-- Chọn Chủ đề --</option>
                        @foreach ($topics as $tp)
                            <option value="{{ $tp->id }}" {{ old('topic_id', $selectedTopicId) == $tp->id ? 'selected' : '' }}>
                                {{ $tp->icon ?? '📁' }} {{ $tp->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Tiêu đề Bài học <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Ví dụ: Higher Education & University Life"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Đường dẫn tĩnh (Slug - Để trống để tự sinh)
                    </label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="higher-education-and-university-life"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Mô tả tóm tắt nội dung bài học
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Bài học cung cấp 10 từ vựng và cụm từ then chốt về giáo dục bậc cao, học phí và phương pháp giảng dạy..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Trình độ Band <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="level"
                            name="level"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->value }}" {{ old('level', 'band_4_5_5_0') === $lv->value ? 'selected' : '' }}>
                                    {{ $lv->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Est Minutes -->
                    <div>
                        <label for="estimated_minutes" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Thời gian học (Phút) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="estimated_minutes"
                            name="estimated_minutes"
                            value="{{ old('estimated_minutes', 15) }}"
                            min="1"
                            max="180"
                            required
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Thứ tự hiển thị
                        </label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="{{ old('sort_order', 0) }}"
                            min="0"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Status -->
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
                                <option value="{{ $st->value }}" {{ old('status', 'published') === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Published At -->
                    <div>
                        <label for="published_at" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Thời điểm xuất bản (Hẹn giờ)
                        </label>
                        <input
                            type="datetime-local"
                            id="published_at"
                            name="published_at"
                            value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Học viên chỉ xem được khi bài học đến hoặc sau thời điểm này</p>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div>
                    <label for="thumbnail" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Ảnh minh họa bài học (Tùy chọn)
                    </label>
                    <input
                        type="file"
                        id="thumbnail"
                        name="thumbnail"
                        accept="image/*"
                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-indigo-400 hover:file:bg-slate-700 cursor-pointer"
                    >
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.vocabulary.lessons.index') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                        Hủy
                    </a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition cursor-pointer shadow-sm">
                        Lưu Bài học & Tiếp tục thêm Từ vựng
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>
