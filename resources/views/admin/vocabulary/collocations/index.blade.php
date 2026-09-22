<x-layouts.admin title="Quản lý Collocation & Cụm từ Học thuật">
    <div class="space-y-6">
        
        <!-- Header & Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-2">
                    <span>📚 Vocabulary & Phraseology</span>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Quản lý Collocations & Phrasal Patterns</h1>
                <p class="text-sm text-slate-400 mt-1">Các cụm từ cố định, cấu trúc diễn đạt tự nhiên và học thuật cho IELTS Writing Band 6.0 - 8.5+</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vocabulary.topics.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition border border-slate-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Quản lý Từ vựng theo Chủ đề
                </a>
                <a href="{{ route('admin.vocabulary.collocations.create') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm Collocation Mới
                </a>
            </div>
        </div>

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-xl border border-indigo-500/20">
                    📖
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Tổng Collocations</span>
                    <div class="text-2xl font-bold text-white mt-0.5">{{ number_format($stats['total']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-xl border border-emerald-500/20">
                    ✓
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Đã xuất bản</span>
                    <div class="text-2xl font-bold text-emerald-400 mt-0.5">{{ number_format($stats['published']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xl border border-amber-500/20">
                    📝
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Bản nháp / Chưa duyệt</span>
                    <div class="text-2xl font-bold text-amber-400 mt-0.5">{{ number_format($stats['draft']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold text-xl border border-purple-500/20">
                    ✨
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Cụm diễn đạt học thuật</span>
                    <div class="text-2xl font-bold text-purple-400 mt-0.5">{{ number_format($stats['academic_phrases']) }}</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <form method="GET" action="{{ route('admin.vocabulary.collocations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo cụm từ, nghĩa hoặc ví dụ..."
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <select
                        name="topic_id"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả chủ đề</option>
                        @foreach ($topics as $tp)
                            <option value="{{ $tp->id }}" {{ (string)$selectedTopicId === (string)$tp->id ? 'selected' : '' }}>
                                {{ $tp->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select
                        name="type"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả cấu trúc</option>
                        @foreach ($types as $tp)
                            <option value="{{ $tp->value }}" {{ $selectedType === $tp->value ? 'selected' : '' }}>
                                {{ $tp->shortLabel() }} ({{ $tp->value }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select
                        name="level"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Tất cả trình độ</option>
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->value }}" {{ $selectedLevel === $lv->value ? 'selected' : '' }}>
                                {{ $lv->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        name="status"
                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ $selectedStatus === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="min-h-[44px] px-4 py-2 rounded-xl text-sm font-semibold text-white bg-slate-700 hover:bg-slate-600 transition cursor-pointer">
                        Lọc
                    </button>
                    @if ($search || $selectedTopicId || $selectedType || $selectedLevel || $selectedStatus)
                        <a href="{{ route('admin.vocabulary.collocations.index') }}" class="min-h-[44px] px-3 py-2 inline-flex items-center justify-center rounded-xl text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition" title="Đặt lại bộ lọc">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Collocations Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
            @if ($collocations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-800/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 w-12 text-center">STT</th>
                                <th class="py-3.5 px-4 min-w-[220px]">Collocation & Ý nghĩa</th>
                                <th class="py-3.5 px-4">Loại cụm từ</th>
                                <th class="py-3.5 px-4">Chủ đề</th>
                                <th class="py-3.5 px-4 text-center">Band Target</th>
                                <th class="py-3.5 px-4 min-w-[280px]">Ví dụ ngữ cảnh IELTS</th>
                                <th class="py-3.5 px-4 text-center">Trạng thái</th>
                                <th class="py-3.5 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($collocations as $collocation)
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="py-4 px-4 text-center text-slate-500 font-mono text-xs">
                                        {{ $collocation->sort_order }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-white text-base font-serif tracking-tight">
                                            {{ $collocation->phrase }}
                                        </div>
                                        <div class="text-xs text-slate-300 mt-0.5">
                                            {{ $collocation->meaning }}
                                        </div>
                                        @if ($collocation->writing_notes)
                                            <div class="mt-1.5 inline-flex items-center gap-1 text-[11px] text-amber-300/90 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                                <span>💡 {{ Str::limit($collocation->writing_notes, 60) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $collocation->type->badgeClass() }}">
                                            {{ $collocation->type->shortLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if ($collocation->topic)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                                <span>{{ $collocation->topic->icon ?? '📚' }}</span>
                                                <span>{{ $collocation->topic->title }}</span>
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-500 italic">Chung (General)</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $collocation->level->badgeClass() }} font-mono">
                                            {{ $collocation->level->shortLabel() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if ($collocation->example_sentence)
                                            <div class="text-xs text-slate-300 italic line-clamp-2">
                                                "{{ $collocation->example_sentence }}"
                                            </div>
                                            @if ($collocation->example_sentence_vi)
                                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">
                                                    {{ $collocation->example_sentence_vi }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-600">Chưa có câu ví dụ</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <form method="POST" action="{{ route('admin.vocabulary.collocations.toggle-status', $collocation) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $collocation->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : ($collocation->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20' : 'bg-slate-700 text-slate-400') }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $collocation->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-400' : ($collocation->status === \App\Enums\ContentStatus::DRAFT ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
                                                {{ $collocation->status->label() }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.vocabulary.collocations.edit', $collocation) }}" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition" title="Chỉnh sửa Collocation">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.vocabulary.collocations.destroy', $collocation) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa collocation này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center min-h-[36px] px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition cursor-pointer" title="Xóa Collocation">
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
                    {{ $collocations->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        📖
                    </div>
                    <h3 class="text-base font-bold text-white">Chưa có collocation nào phù hợp</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Thêm các cụm từ collocation học thuật (Ví dụ: "play a pivotal role", "address climate change") để giúp học viên nâng điểm tiêu chí Lexical Resource.
                    </p>
                    <div class="mt-5">
                        <a href="{{ route('admin.vocabulary.collocations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 transition">
                            + Thêm Collocation đầu tiên
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
