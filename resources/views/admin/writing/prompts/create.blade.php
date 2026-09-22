<x-layouts.admin title="Thêm Đề bài Writing Mới">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Breadcrumb & Actions -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.writing.prompts.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                    ← Quay lại danh sách Đề bài
                </a>
                <h1 class="text-2xl font-extrabold text-white mt-1">Thêm Đề bài IELTS Writing Mới</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.writing.prompts.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Task Type & Classification -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-5">
                <h2 class="text-base font-bold text-white pb-3 border-b border-slate-800">
                    1. Phân loại & Thông tin cơ bản
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Task Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Dạng Task <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="task_type"
                            id="task_type"
                            onchange="syncTaskDefaults(this.value)"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($taskTypes as $tt)
                                <option value="{{ $tt->value }}" @selected(old('task_type') === $tt->value)>
                                    {{ $tt->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prompt Type -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Dạng biểu đồ / Nghị luận <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="prompt_type"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($promptTypesGrouped as $groupName => $options)
                                <optgroup label="{{ $groupName }}" class="bg-slate-900 text-slate-400">
                                    @foreach ($options as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('prompt_type') === $val) class="bg-slate-800 text-white">
                                            {{ $lbl }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('prompt_type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Topic Category -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Chủ đề Từ vựng liên kết
                        </label>
                        <select
                            name="topic_id"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">-- Không liên kết chủ đề --</option>
                            @foreach ($topics as $tp)
                                <option value="{{ $tp->id }}" @selected(old('topic_id') == $tp->id)>
                                    {{ $tp->icon ?? '📁' }} {{ $tp->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Trình độ khuyến nghị <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="level"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->value }}" @selected(old('level', 'band_4_5_5_0') === $lv->value)>
                                    {{ $lv->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Title & Slug -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Tiêu đề Đề bài <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="e.g. Proportion of Elderly Population in 3 Countries"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                        @error('title') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Slug (Đường dẫn tĩnh)
                        </label>
                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="Tự động tạo nếu để trống..."
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                        @error('slug') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Min Words & Time Limit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Số từ tối thiểu <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="min_words"
                            id="min_words"
                            value="{{ old('min_words', 150) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Thời gian làm bài gợi ý (phút) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="time_limit_minutes"
                            id="time_limit_minutes"
                            value="{{ old('time_limit_minutes', 20) }}"
                            class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                </div>
            </div>

            <!-- Section 2: Diagram Image & Prompt Content -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white">
                            2. Hình ảnh Sơ đồ & Nội dung Đề bài
                        </h2>
                        <p class="text-xs text-slate-400">Tải lên sơ đồ/biểu đồ cho Task 1 và sử dụng tính năng OCR tự động đọc đề từ ảnh.</p>
                    </div>
                </div>

                <!-- Image Upload & OCR Box -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Hình ảnh Sơ đồ / Biểu đồ / Bản đồ (PNG, JPG, SVG, WebP tối đa 5MB)
                    </label>

                    <!-- Dropzone Container -->
                    <div
                        id="image-dropzone"
                        ondragover="event.preventDefault(); this.classList.add('border-indigo-500', 'bg-indigo-500/5')"
                        ondragleave="event.preventDefault(); this.classList.remove('border-indigo-500', 'bg-indigo-500/5')"
                        ondrop="event.preventDefault(); this.classList.remove('border-indigo-500', 'bg-indigo-500/5'); handleImageSelected(event.dataTransfer.files)"
                        onclick="document.getElementById('prompt-image-input').click()"
                        class="border-2 border-dashed border-slate-700 hover:border-indigo-500/80 bg-slate-800/40 hover:bg-slate-800/70 rounded-2xl p-6 text-center transition cursor-pointer group"
                    >
                        <input
                            type="file"
                            id="prompt-image-input"
                            name="image"
                            accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            onchange="handleImageSelected(this.files)"
                            class="hidden"
                        >

                        <div id="dropzone-idle" class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xl font-bold flex items-center justify-center mx-auto group-hover:scale-110 transition">
                                🖼️
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-200">
                                    Kéo thả hình ảnh sơ đồ vào đây, hoặc <span class="text-indigo-400 underline decoration-indigo-400/40">chọn từ thiết bị</span>
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Hỗ trợ ảnh đề thi Cambridge, biểu đồ cột, đường, tròn, bản đồ, quy trình...
                                </p>
                            </div>
                        </div>

                        <!-- Image Selected Preview -->
                        <div id="dropzone-preview" class="hidden space-y-4" onclick="event.stopPropagation()">
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 bg-slate-900/90 border border-slate-700/80 p-4 rounded-2xl max-w-xl mx-auto">
                                <img id="preview-img" src="" alt="Sơ đồ preview" class="max-h-44 max-w-full object-contain rounded-xl border border-slate-700 bg-slate-950">
                                <div class="text-left space-y-2">
                                    <div>
                                        <span id="preview-filename" class="text-xs font-bold text-white block truncate max-w-xs">--</span>
                                        <span id="preview-filesize" class="text-[11px] font-mono text-slate-400">--</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 pt-1">
                                        <button
                                            type="button"
                                            id="btn-ocr-scan"
                                            onclick="triggerOcrScanning()"
                                            class="min-h-[38px] inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition cursor-pointer"
                                        >
                                            <span>✨ Đọc đề có trong ảnh (OCR)</span>
                                        </button>

                                        <button
                                            type="button"
                                            onclick="document.getElementById('prompt-image-input').click()"
                                            class="min-h-[38px] px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-bold transition cursor-pointer"
                                        >
                                            Đổi ảnh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OCR Progress Indicator -->
                    <div id="ocr-progress-card" class="hidden p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span id="ocr-status-text" class="font-bold text-indigo-300 flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Đang khởi tạo bộ giải mã OCR...
                            </span>
                            <span id="ocr-percentage" class="font-mono font-bold text-indigo-400">0%</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div id="ocr-progress-bar" class="bg-indigo-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- OCR Result Notification -->
                    <div id="ocr-result-card" class="hidden p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-300">
                                <span>🎉</span>
                                <span>Đã đọc đề thành công và tự động điền vào biểu mẫu bên dưới!</span>
                            </div>
                            <button type="button" onclick="document.getElementById('ocr-raw-container').classList.toggle('hidden')" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 underline">
                                Xem văn bản thô
                            </button>
                        </div>
                        <div id="ocr-raw-container" class="hidden pt-2">
                            <pre id="ocr-raw-text" class="text-xs font-mono text-slate-300 bg-slate-950 p-3 rounded-xl border border-slate-800 whitespace-pre-wrap max-h-40 overflow-y-auto"></pre>
                        </div>
                    </div>

                    @error('image') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Prompt English Textarea -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Đề bài Tiếng Anh đầy đủ <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        name="prompt_text"
                        id="prompt_text"
                        rows="4"
                        placeholder="Nhập hoặc tự động điền đề bài từ nút 'Đọc đề có trong ảnh'..."
                        class="w-full p-3.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300"
                    >{{ old('prompt_text') }}</textarea>
                    @error('prompt_text') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Guidance & Strategy -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Hướng dẫn phân tích đề & Chiến lược làm bài (Guidance)
                    </label>
                    <textarea
                        name="guidance"
                        rows="3"
                        placeholder="Gợi ý phương pháp phân tích số liệu, mẹo tránh bẫy cho Band 4.5 – 5.0..."
                        class="w-full p-3.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >{{ old('guidance') }}</textarea>
                </div>
            </div>

            <!-- Section 3: Suggested Outline Builder -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white">3. Dàn ý gợi ý chi tiết (Suggested Outline)</h2>
                        <p class="text-xs text-slate-400">Giúp người học band 4.5 – 5.0 biết cách triển khai từng đoạn văn.</p>
                    </div>
                    <button type="button" onclick="addOutlineRow()" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold hover:bg-indigo-500/20 transition cursor-pointer">
                        + Thêm đoạn
                    </button>
                </div>

                <div id="outline-rows" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-800/60 rounded-2xl border border-slate-700/60 outline-row">
                        <div>
                            <input type="text" name="outline_sections[0][section]" value="Introduction" placeholder="Tên đoạn (e.g. Introduction)" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-bold">
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2">
                            <input type="text" name="outline_sections[0][hint]" value="Paraphrase đề bài bằng các từ đồng nghĩa..." placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs placeholder:text-slate-500">
                            <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-400 text-xs font-bold cursor-pointer">✕</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-800/60 rounded-2xl border border-slate-700/60 outline-row">
                        <div>
                            <input type="text" name="outline_sections[1][section]" value="Overview" placeholder="Tên đoạn (e.g. Overview)" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-bold">
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2">
                            <input type="text" name="outline_sections[1][hint]" value="Nêu 2 đặc điểm tổng quan nổi bật nhất..." placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs placeholder:text-slate-500">
                            <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-400 text-xs font-bold cursor-pointer">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Status & Publish -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-white pb-3 border-b border-slate-800">4. Xuất bản</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Trạng thái <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}" @selected(old('status', 'published') === $st->value)>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Thứ tự sắp xếp
                        </label>
                        <input type="number" name="order_index" value="{{ old('order_index', 0) }}" class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.writing.prompts.index') }}" class="min-h-[44px] px-6 py-2.5 rounded-2xl border border-slate-700 text-xs font-bold text-slate-300 hover:bg-slate-800 transition flex items-center">
                    Hủy bỏ
                </a>
                <button type="submit" class="min-h-[44px] px-8 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    Lưu Đề bài & Tiếp tục →
                </button>
            </div>
        </form>

    </div>

    <!-- Tesseract OCR Engine -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

    <script>
        let currentSelectedFile = null;

        function syncTaskDefaults(val) {
            if (val === 'task_1') {
                document.getElementById('min_words').value = 150;
                document.getElementById('time_limit_minutes').value = 20;
            } else {
                document.getElementById('min_words').value = 250;
                document.getElementById('time_limit_minutes').value = 40;
            }
        }

        function handleImageSelected(files) {
            if (!files || files.length === 0) return;
            const file = files[0];
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file hình ảnh hợp lệ (PNG, JPG, WebP, SVG).');
                return;
            }

            currentSelectedFile = file;

            // Sync with file input if dropped
            const input = document.getElementById('prompt-image-input');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            // Show Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-filename').textContent = file.name;
                document.getElementById('preview-filesize').textContent = (file.size / 1024).toFixed(1) + ' KB';

                document.getElementById('dropzone-idle').classList.add('hidden');
                document.getElementById('dropzone-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        async function triggerOcrScanning() {
            if (!currentSelectedFile) {
                alert('Vui lòng chọn hình ảnh sơ đồ trước khi đọc đề.');
                return;
            }

            const btn = document.getElementById('btn-ocr-scan');
            const progressCard = document.getElementById('ocr-progress-card');
            const resultCard = document.getElementById('ocr-result-card');
            const progressBar = document.getElementById('ocr-progress-bar');
            const percentageText = document.getElementById('ocr-percentage');
            const statusText = document.getElementById('ocr-status-text');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            progressCard.classList.remove('hidden');
            resultCard.classList.add('hidden');

            try {
                // Initialize and run Tesseract.js in browser
                const worker = await Tesseract.createWorker('eng', 1, {
                    logger: m => {
                        if (m.status === 'recognizing text') {
                            const pct = Math.round((m.progress || 0) * 100);
                            progressBar.style.width = pct + '%';
                            percentageText.textContent = pct + '%';
                            statusText.innerHTML = `
                                <svg class="animate-spin h-3.5 w-3.5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Đang quét và nhận diện chữ trong ảnh: ${pct}%...
                            `;
                        } else if (m.status === 'loading tesseract core' || m.status === 'initializing tesseract') {
                            statusText.textContent = 'Đang tải bộ giải mã OCR...';
                        }
                    }
                });

                const ret = await worker.recognize(currentSelectedFile);
                await worker.terminate();

                const extractedText = ret.data.text;

                statusText.textContent = 'Đang cấu trúc hóa đề bài IELTS...';
                progressBar.style.width = '100%';
                percentageText.textContent = '100%';

                // Send to server endpoint for smart parser and structured fields
                const response = await fetch('{{ route('admin.writing.prompts.ocr-image') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ raw_text: extractedText })
                });

                const json = await response.json();
                if (json.success && json.data) {
                    applyParsedData(json.data);
                } else {
                    // Fallback to client-side filling if endpoint fails
                    document.getElementById('prompt_text').value = extractedText;
                    highlightInput(document.getElementById('prompt_text'));
                }

                // Show raw text
                document.getElementById('ocr-raw-text').textContent = extractedText;
                resultCard.classList.remove('hidden');

            } catch (err) {
                console.error('OCR Error:', err);
                alert('Không thể nhận diện chữ từ ảnh: ' + (err.message || 'Lỗi không xác định'));
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                progressCard.classList.add('hidden');
            }
        }

        function applyParsedData(data) {
            // Auto fill Title
            if (data.title) {
                const titleInput = document.querySelector('[name="title"]');
                if (titleInput) {
                    titleInput.value = data.title;
                    highlightInput(titleInput);
                }
            }

            // Auto fill Prompt Text
            if (data.prompt_text) {
                const promptTextArea = document.getElementById('prompt_text');
                if (promptTextArea) {
                    promptTextArea.value = data.prompt_text;
                    highlightInput(promptTextArea);
                }
            }

            // Auto select Task Type
            if (data.task_type) {
                const taskTypeSelect = document.getElementById('task_type');
                if (taskTypeSelect) {
                    taskTypeSelect.value = data.task_type;
                    syncTaskDefaults(data.task_type);
                    highlightInput(taskTypeSelect);
                }
            }

            // Auto select Prompt Type
            if (data.prompt_type) {
                const promptTypeSelect = document.querySelector('[name="prompt_type"]');
                if (promptTypeSelect) {
                    promptTypeSelect.value = data.prompt_type;
                    highlightInput(promptTypeSelect);
                }
            }
        }

        function highlightInput(el) {
            el.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-950/20');
            setTimeout(() => {
                el.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-950/20');
            }, 3000);
        }

        let outlineIdx = 2;
        function addOutlineRow() {
            const container = document.getElementById('outline-rows');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2 p-3 bg-slate-800/60 rounded-2xl border border-slate-700/60 outline-row';
            row.innerHTML = `
                <div>
                    <input type="text" name="outline_sections[${outlineIdx}][section]" placeholder="Tên đoạn (e.g. Body 1)" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-bold">
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="text" name="outline_sections[${outlineIdx}][hint]" placeholder="Gợi ý nội dung triển khai..." class="flex-1 px-3 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs placeholder:text-slate-500">
                    <button type="button" onclick="this.closest('.outline-row').remove()" class="p-2 text-slate-400 hover:text-rose-400 text-xs font-bold cursor-pointer">✕</button>
                </div>
            `;
            container.appendChild(row);
            outlineIdx++;
        }
    </script>
</x-layouts.admin>
