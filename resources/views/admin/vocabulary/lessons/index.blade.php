<x-layouts.admin title="Quản lý Bài học Từ vựng">
    <div class="space-y-6">
        
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Bài học Từ vựng (Vocabulary Lessons)</h1>
                <p class="text-sm text-slate-400 mt-1">Quản lý các bài học từ vựng IELTS theo Level và Chủ đề</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vocabulary.topics.index') }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 transition">
                    Quản lý Chủ đề
                </a>
                <a href="{{ route('admin.vocabulary.lessons.create', ['topic_id' => $selectedTopic]) }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tạo Bài học Mới
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <form method="GET" action="{{ route('admin.vocabulary.lessons.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo tiêu đề bài học hoặc mô tả..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Topic Filter -->
                <div>
                    <select
                        name="topic_id"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả chủ đề</option>
                        @foreach ($topics as $tp)
                            <option value="{{ $tp->id }}" {{ $selectedTopic == $tp->id ? 'selected' : '' }}>
                                {{ $tp->icon ?? '📁' }} {{ $tp->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Filter -->
                <div>
                    <select
                        name="level"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả trình độ</option>
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->value }}" {{ $selectedLevel === $lv->value ? 'selected' : '' }}>
                                {{ $lv->shortLabel() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter & Submit -->
                <div class="flex items-center gap-2">
                    <select
                        name="status"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ $selectedStatus === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="min-h-[44px] px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition cursor-pointer shrink-0">
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Lessons List -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
            @if ($lessons->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-800/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">STT</th>
                                <th class="py-3.5 px-4">Bài học</th>
                                <th class="py-3.5 px-4">Chủ đề</th>
                                <th class="py-3.5 px-4 text-center">Trình độ</th>
                                <th class="py-3.5 px-4 text-center">Số từ vựng</th>
                                <th class="py-3.5 px-4 text-center">Trạng thái</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($lessons as $lesson)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-4 text-center text-slate-500 font-mono text-xs">
                                        {{ $lesson->sort_order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            @if ($lesson->thumbnail_url)
                                                <img src="{{ $lesson->thumbnail_url }}" alt="{{ $lesson->title }}" class="w-12 h-12 rounded-xl object-cover ring-1 ring-slate-700 bg-slate-800">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center font-bold text-lg">
                                                    📖
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="font-bold text-white hover:text-indigo-400 transition">
                                                    {{ $lesson->title }}
                                                </a>
                                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                                    <span>⏱ {{ $lesson->estimated_minutes }} phút</span>
                                                    <span>•</span>
                                                    <span>{{ $lesson->published_at ? $lesson->published_at->format('d/m/Y') : 'Chưa hẹn giờ' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                                            <span>{{ $lesson->topic?->icon ?? '📁' }}</span>
                                            <span>{{ $lesson->topic?->title ?? 'N/A' }}</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $lesson->level->badgeClass() }}">
                                            {{ $lesson->level->shortLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 transition">
                                            <span>{{ $lesson->items_count }} từ</span>
                                            <span class="text-[10px]">→</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <form method="POST" action="{{ route('admin.vocabulary.lessons.toggle-status', $lesson) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $lesson->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : ($lesson->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20' : 'bg-slate-700 text-slate-400') }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $lesson->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-400' : ($lesson->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
                                                {{ $lesson->status->label() }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-1.5">
                                        <a href="{{ route('admin.vocabulary.lessons.preview', $lesson) }}" class="inline-flex items-center min-h-[36px] px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/10 transition" title="Xem thử bài học">
                                            Xem thử
                                        </a>
                                        <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="inline-flex items-center min-h-[36px] px-2.5 py-1.5 rounded-lg text-xs font-semibold text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 transition" title="Quản lý danh sách từ vựng">
                                            Từ vựng
                                        </a>
                                        <a href="{{ route('admin.vocabulary.lessons.edit', $lesson) }}" class="inline-flex items-center min-h-[36px] px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Chỉnh sửa bài học">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.vocabulary.lessons.destroy', $lesson) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài học này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center min-h-[36px] px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition cursor-pointer" title="Xóa bài học">
                                                Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800">
                    {{ $lessons->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        📖
                    </div>
                    <h3 class="text-base font-bold text-white">Chưa tìm thấy bài học nào</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Hãy tạo bài học từ vựng mới và phân loại vào các chủ đề IELTS tương ứng.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('admin.vocabulary.lessons.create', ['topic_id' => $selectedTopic]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                            + Tạo Bài học Mới
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
