<x-layouts.admin title="Quản lý Chủ đề Từ vựng">
    <div class="space-y-6">
        
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Chủ đề Từ vựng (Vocabulary Topics)</h1>
                <p class="text-sm text-slate-400 mt-1">Phân loại các bài học từ vựng IELTS theo chủ đề học thuật</p>
            </div>
            <a href="{{ route('admin.vocabulary.topics.create') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Thêm Chủ đề Mới
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <form method="GET" action="{{ route('admin.vocabulary.topics.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo tên chủ đề hoặc mô tả..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div class="w-full sm:w-48">
                    <select
                        name="status"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="min-h-[44px] px-4 py-2 rounded-xl text-sm font-semibold text-white bg-slate-700 hover:bg-slate-600 transition cursor-pointer w-full sm:w-auto">
                        Lọc
                    </button>
                    @if ($search || $status)
                        <a href="{{ route('admin.vocabulary.topics.index') }}" class="min-h-[44px] px-4 py-2 inline-flex items-center justify-center rounded-xl text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                            Đặt lại
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Topics Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
            @if ($topics->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-800/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">STT</th>
                                <th class="py-3.5 px-4">Chủ đề</th>
                                <th class="py-3.5 px-4 text-center">Số bài học</th>
                                <th class="py-3.5 px-4 text-center">Tổng số từ</th>
                                <th class="py-3.5 px-4 text-center">Trạng thái</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($topics as $index => $topic)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-4 text-center text-slate-500 font-mono text-xs">
                                        {{ $topic->sort_order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            @if ($topic->image_url)
                                                <img src="{{ $topic->image_url }}" alt="{{ $topic->title }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-700 bg-slate-800">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-base">
                                                    {{ $topic->icon ?? '📚' }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $topic->id]) }}" class="font-bold text-white hover:text-indigo-400 transition">
                                                    {{ $topic->title }}
                                                </a>
                                                <div class="text-xs text-slate-500 font-mono mt-0.5">/{{ $topic->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $topic->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 transition">
                                            {{ $topic->lessons_count }} bài học
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-center font-semibold text-slate-300">
                                        {{ $topic->items_count }} từ
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <form method="POST" action="{{ route('admin.vocabulary.topics.toggle-status', $topic) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $topic->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : ($topic->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20' : 'bg-slate-700 text-slate-400') }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $topic->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-400' : ($topic->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
                                                {{ $topic->status->label() }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2">
                                        <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $topic->id]) }}" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 transition" title="Xem danh sách bài học">
                                            Bài học
                                        </a>
                                        <a href="{{ route('admin.vocabulary.topics.edit', $topic) }}" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Chỉnh sửa chủ đề">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.vocabulary.topics.destroy', $topic) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chủ đề này? Toàn bộ bài học và từ vựng thuộc chủ đề sẽ bị xóa.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition cursor-pointer" title="Xóa chủ đề">
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
                    {{ $topics->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        📂
                    </div>
                    <h3 class="text-base font-bold text-white">Chưa tìm thấy chủ đề nào</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Hãy tạo các chủ đề đầu tiên như Education, Environment, Technology để phân loại bài học.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('admin.vocabulary.topics.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                            + Thêm Chủ đề mới
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
