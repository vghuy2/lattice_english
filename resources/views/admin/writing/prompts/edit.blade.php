<x-layouts.admin title="Chỉnh sửa Đề bài Writing">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Breadcrumb & Actions -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    ← Quay lại chi tiết Đề bài
                </a>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Chỉnh sửa Đề bài IELTS Writing</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.writing.prompts.update', $prompt) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Task Type & Classification -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">
                    1. Phân loại & Thông tin cơ bản
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Task Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Dạng Task <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="task_type"
                            id="task_type"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                            @foreach ($taskTypes as $tt)
                                <option value="{{ $tt->value }}" @selected(old('task_type', $prompt->task_type->value) === $tt->value)>
                                    {{ $tt->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prompt Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Dạng biểu đồ / Nghị luận <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="prompt_type"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                            @foreach ($promptTypesGrouped as $groupName => $options)
                                <optgroup label="{{ $groupName }}">
                                    @foreach ($options as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('prompt_type', $prompt->prompt_type->value) === $val)>
                                            {{ $lbl }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('prompt_type') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Topic Category -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Chủ đề Từ vựng liên kết
                        </label>
                        <select
                            name="topic_id"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                            <option value="">-- Không liên kết chủ đề --</option>
                            @foreach ($topics as $tp)
                                <option value="{{ $tp->id }}" @selected(old('topic_id', $prompt->topic_id) == $tp->id)>
                                    {{ $tp->icon ?? '📁' }} {{ $tp->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Trình độ khuyến nghị <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="level"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->value }}" @selected(old('level', $prompt->level->value) === $lv->value)>
                                    {{ $lv->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Title & Slug -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tiêu đề Đề bài <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $prompt->title) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                        @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Slug (Đường dẫn tĩnh) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug', $prompt->slug) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                        @error('slug') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Min Words & Time Limit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Số từ tối thiểu <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="min_words"
                            value="{{ old('min_words', $prompt->min_words) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Thời gian làm bài gợi ý (phút) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="time_limit_minutes"
                            value="{{ old('time_limit_minutes', $prompt->time_limit_minutes) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-600"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 2: Prompt Content & Chart Image -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">
                    2. Nội dung Đề bài & Hình ảnh Biểu đồ (Task 1)
                </h2>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Đề bài Tiếng Anh đầy đủ <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        name="prompt_text"
                        rows="4"
                        class="w-full p-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >{{ old('prompt_text', $prompt->prompt_text) }}</textarea>
                    @error('prompt_text') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                @if ($prompt->image_path)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-4">
                        <img src="{{ $prompt->image_url }}" alt="Chart preview" class="w-24 h-24 object-cover rounded-xl border border-slate-300 bg-white">
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-700 block">Ảnh biểu đồ hiện tại</span>
                            <label class="inline-flex items-center gap-2 text-xs text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600">
                                <span>Xóa ảnh biểu đồ này</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        {{ $prompt->image_path ? 'Thay đổi ảnh biểu đồ mới' : 'Tải lên ảnh Biểu đồ / Bản đồ (PNG, JPG, SVG, WebP)' }}
                    </label>
                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl text-sm"
                    >
                    @error('image') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Hướng dẫn phân tích đề & Chiến lược làm bài (Guidance)
                    </label>
                    <textarea
                        name="guidance"
                        rows="3"
                        class="w-full p-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >{{ old('guidance', $prompt->guidance) }}</textarea>
                </div>
            </div>

            <!-- Section 3: Suggested Outline Builder -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">3. Dàn ý gợi ý chi tiết (Suggested Outline)</h2>
                        <p class="text-xs text-slate-500">Gợi ý phân chia đoạn văn cho người học band 4.5 – 5.0.</p>
                    </div>
                    <button type="button" onclick="addOutlineRow()" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition cursor-pointer">
                        + Thêm đoạn
                    </button>
                </div>

                <div id="outline-rows" class="space-y-3">
                    @php $outline = old('outline_sections', $prompt->suggested_outline ?? []); @endphp
                    @forelse ($outline as $idx => $sec)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 outline-row">
                            <div>
                                <input type="text" name="outline_sections[{{ $idx }}][section]" value="{{ $sec['section'] ?? '' }}" placeholder="Tên đoạn" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="text" name="outline_sections[{{ $idx }}][hint]" value="{{ $sec['hint'] ?? '' }}" placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @empty
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 outline-row">
                            <div>
                                <input type="text" name="outline_sections[0][section]" value="Introduction" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="text" name="outline_sections[0][hint]" placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                                <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 4: Status & Publish -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100">4. Xuất bản</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Trạng thái <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}" @selected(old('status', $prompt->status->value) === $st->value)>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Thứ tự sắp xếp
                        </label>
                        <input type="number" name="order_index" value="{{ old('order_index', $prompt->order_index) }}" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="min-h-[44px] px-6 py-2.5 rounded-2xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition flex items-center">
                    Hủy bỏ
                </a>
                <button type="submit" class="min-h-[44px] px-8 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Cập nhật Đề bài →
                </button>
            </div>
        </form>

    </div>

    <script>
        let outlineIdx = {{ count($outline ?? []) + 10 }};
        function addOutlineRow() {
            const container = document.getElementById('outline-rows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200 outline-row';
            row.innerHTML = `
                <div>
                    <input type="text" name="outline_sections[${outlineIdx}][section]" placeholder="Tên đoạn (e.g. Body 1)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold">
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="text" name="outline_sections[${outlineIdx}][hint]" placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold">✕</button>
                </div>
            `;
            container.appendChild(row);
            outlineIdx++;
        }
    </script>
</x-layouts.admin>
