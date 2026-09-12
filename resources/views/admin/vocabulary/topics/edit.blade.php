<x-layouts.admin title="Chỉnh sửa Chủ đề: {{ $topic->title }}">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.vocabulary.topics.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2">
                    ← Quay lại danh sách chủ đề
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Chỉnh sửa Chủ đề: {{ $topic->title }}</h1>
            </div>
            <div>
                <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $topic->id]) }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 transition">
                    Xem {{ $topic->lessons()->count() }} bài học thuộc chủ đề →
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('admin.vocabulary.topics.update', $topic) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Tên Chủ đề <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $topic->title) }}"
                        required
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Đường dẫn tĩnh (Slug)
                    </label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $topic->slug) }}"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Mô tả tóm tắt chủ đề
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >{{ old('description', $topic->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Emoji / Icon
                        </label>
                        <input
                            type="text"
                            id="icon"
                            name="icon"
                            value="{{ old('icon', $topic->icon) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                            value="{{ old('sort_order', $topic->sort_order) }}"
                            min="0"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                        >
                    </div>

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
                                <option value="{{ $st->value }}" {{ old('status', $topic->status->value) === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Image Upload & Preview -->
                <div>
                    <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Ảnh bìa chủ đề
                    </label>
                    @if ($topic->image_url)
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ $topic->image_url }}" alt="{{ $topic->title }}" class="w-16 h-16 rounded-xl object-cover ring-1 ring-slate-700 bg-slate-800">
                            <span class="text-xs text-slate-400">Ảnh hiện tại</span>
                        </div>
                    @endif
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-indigo-400 hover:file:bg-slate-700 cursor-pointer"
                    >
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.vocabulary.topics.index') }}" class="min-h-[44px] px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                        Hủy
                    </a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition cursor-pointer shadow-sm">
                        Cập nhật Chủ đề
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.admin>
