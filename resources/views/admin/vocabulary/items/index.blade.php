<x-layouts.admin title="Quản lý Từ vựng: {{ $lesson->title }}">
    <div class="space-y-6">
        
        <!-- Header & Top Links -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 mb-2">
                    ← Quay lại danh sách bài học
                </a>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-white tracking-tight">Từ vựng: {{ $lesson->title }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $lesson->level->badgeClass() }}">
                        {{ $lesson->level->shortLabel() }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vocabulary.lessons.preview', $lesson) }}" class="min-h-[44px] inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                    👁 Xem thử (Preview)
                </a>
                <a href="{{ route('admin.vocabulary.lessons.items.create', $lesson) }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm Từ Mới
                </a>
            </div>
        </div>

        <!-- Filter / Search in Lesson -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <form method="GET" action="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="flex items-center gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo từ, nghĩa tiếng Việt hoặc câu ví dụ..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
                <button type="submit" class="min-h-[44px] px-5 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 transition cursor-pointer">
                    Tìm
                </button>
                @if ($search)
                    <a href="{{ route('admin.vocabulary.lessons.items.index', $lesson) }}" class="min-h-[44px] px-4 py-2 inline-flex items-center rounded-xl text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 transition">
                        Đặt lại
                    </a>
                @endif
            </form>
        </div>

        <!-- Items Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
            @if ($items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-800/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">STT</th>
                                <th class="py-3.5 px-4">Từ / Cụm từ</th>
                                <th class="py-3.5 px-4">Loại từ</th>
                                <th class="py-3.5 px-4">Nghĩa tiếng Việt</th>
                                <th class="py-3.5 px-4">Câu ví dụ & Collocations</th>
                                <th class="py-3.5 px-4 text-center">Writing Notes</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($items as $item)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-4 text-center text-slate-500 font-mono text-xs">
                                        {{ $item->sort_order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-white text-base">
                                            {{ $item->word }}
                                        </div>
                                        @if ($item->ipa)
                                            <div class="text-xs text-indigo-400 font-mono mt-0.5">
                                                {{ $item->ipa }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                            {{ $item->part_of_speech->short() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 font-semibold text-emerald-400 max-w-[200px] truncate" title="{{ $item->vietnamese_meaning }}">
                                        {{ $item->vietnamese_meaning }}
                                    </td>
                                    <td class="py-4 px-4 max-w-[280px]">
                                        <p class="text-xs text-slate-300 italic truncate" title="{{ $item->example_sentence }}">
                                            "{{ $item->example_sentence }}"
                                        </p>
                                        @if (!empty($item->collocations))
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach (array_slice($item->collocations, 0, 2) as $c)
                                                    <span class="px-2 py-0.5 rounded text-[10px] bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">{{ $c }}</span>
                                                @endforeach
                                                @if (count($item->collocations) > 2)
                                                    <span class="text-[10px] text-slate-500">+{{ count($item->collocations) - 2 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if ($item->writing_notes)
                                            <span class="px-2 py-1 rounded text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20" title="{{ $item->writing_notes }}">
                                                ✍️ Có ghi chú
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-600">--</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2">
                                        <a href="{{ route('admin.vocabulary.lessons.items.edit', [$lesson, $item]) }}" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.vocabulary.lessons.items.destroy', [$lesson, $item]) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa từ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition cursor-pointer">
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
                    {{ $items->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        📝
                    </div>
                    <h3 class="text-base font-bold text-white">Chưa có từ vựng nào trong bài học này</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Thêm các từ vựng kèm ngữ cảnh, câu ví dụ, collocations và ghi chú IELTS Writing để học viên bắt đầu luyện tập.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('admin.vocabulary.lessons.items.create', $lesson) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                            + Thêm Từ vựng Mới
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
