<x-layouts.admin title="Quản lý IELTS Writing Task 2">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-2">
                    <span>💡 Task 2: Academic Discursive Essay</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Quản lý Writing Task 2 (Nghị luận & Quan điểm)
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    Ngân hàng đề Opinion, Discussion, Problem-Solution, Advantages-Disadvantages & Two-Part Essay có dàn ý và bài mẫu học thuật.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.writing.task1.index') }}" class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs border border-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Chuyển sang Writing Task 1</span>
                </a>
                <a href="{{ route('admin.writing.prompts.create', ['task_type' => 'task_2']) }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Thêm Đề Task 2 Mới</span>
                </a>
            </div>
        </div>

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-xl border border-indigo-500/20">
                    💡
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Tổng số đề Task 2</span>
                    <div class="text-2xl font-bold text-white mt-0.5">{{ number_format($stats['total']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-xl border border-emerald-500/20">
                    ✓
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Đang xuất bản</span>
                    <div class="text-2xl font-bold text-emerald-400 mt-0.5">{{ number_format($stats['published']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xl border border-amber-500/20">
                    📝
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Đã có bài mẫu</span>
                    <div class="text-2xl font-bold text-amber-400 mt-0.5">{{ number_format($stats['with_samples']) }}</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-xl border border-rose-500/20">
                    🔥
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-medium">Band cao (6.5+)</span>
                    <div class="text-2xl font-bold text-rose-400 mt-0.5">{{ number_format($stats['high_band']) }}</div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.writing.task2.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm tiêu đề hoặc câu hỏi luận Task 2..."
                        class="w-full min-h-[44px] px-3.5 py-2 rounded-xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Prompt Type Filter -->
                <div>
                    <select name="prompt_type" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tất cả dạng nghị luận</option>
                        @foreach ($promptTypes as $pt)
                            <option value="{{ $pt->value }}" @selected($selectedPromptType === $pt->value)>
                                {{ $pt->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Filter -->
                <div>
                    <select name="level" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tất cả trình độ</option>
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->value }}" @selected($selectedLevel === $lv->value)>
                                {{ $lv->shortLabel() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter & Button -->
                <div class="flex items-center gap-2">
                    <select name="status" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-700 bg-slate-800 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" @selected($selectedStatus === $st->value)>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="min-h-[44px] px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shrink-0 cursor-pointer shadow-sm">
                        Lọc
                    </button>

                    @if ($search || $selectedPromptType || $selectedLevel || $selectedStatus)
                        <a href="{{ route('admin.writing.task2.index') }}" class="min-h-[44px] px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold inline-flex items-center justify-center transition shrink-0" title="Đặt lại bộ lọc">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Prompts Table / Cards -->
        @if ($prompts->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl text-indigo-400 mb-3">
                    💡
                </div>
                <h3 class="text-base font-bold text-white">Chưa có đề bài Writing Task 2 nào phù hợp</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Thêm các đề nghị luận xã hội dạng Agree/Disagree, Discussion, Problem-Solution với dàn ý chi tiết để học viên luyện viết.
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.writing.prompts.create', ['task_type' => 'task_2']) }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-500 transition">
                        + Tạo đề Task 2 đầu tiên
                    </a>
                </div>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/60 border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Đề bài & Luận điểm Task 2</th>
                                <th class="py-3.5 px-4">Dạng nghị luận</th>
                                <th class="py-3.5 px-4">Mục tiêu & Giới hạn</th>
                                <th class="py-3.5 px-4 text-center">Dàn ý & Bài mẫu</th>
                                <th class="py-3.5 px-4 text-center">Trạng thái</th>
                                <th class="py-3.5 px-6 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-sm">
                            @foreach ($prompts as $prompt)
                                <tr class="hover:bg-slate-800/40 transition">
                                    
                                    <!-- Title & Essay Prompt -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-start gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex flex-col items-center justify-center font-bold text-indigo-400 text-xs shrink-0 p-1 text-center">
                                                <span>✍️</span>
                                                <span class="text-[9px] text-slate-400 mt-0.5 font-mono">T2</span>
                                            </div>

                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold border bg-indigo-500/10 text-indigo-400 border-indigo-500/20">
                                                        {{ $prompt->prompt_type->shortLabel() }}
                                                    </span>
                                                    @if ($prompt->topic)
                                                        <span class="text-xs text-slate-400 font-medium">
                                                            {{ $prompt->topic->icon ?? '📁' }} {{ $prompt->topic->title }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="font-bold text-white hover:text-indigo-400 transition block text-sm">
                                                    {{ $prompt->title }}
                                                </a>

                                                <p class="text-xs text-slate-400 line-clamp-2 max-w-lg leading-relaxed font-serif italic">
                                                    "{{ $prompt->prompt_text }}"
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prompt Type -->
                                    <td class="py-4 px-4 text-xs font-semibold text-slate-300">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300">
                                            {{ $prompt->prompt_type->label() }}
                                        </span>
                                    </td>

                                    <!-- Level & Limits -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-block px-2 py-0.5 rounded-md text-xs font-bold border {{ $prompt->level->badgeClass() }}">
                                                {{ $prompt->level->shortLabel() }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 block font-mono">
                                                ⏱ {{ $prompt->time_limit_minutes }}p • ≥{{ $prompt->min_words }} từ
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Outline & Sample Essays Count -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <a href="{{ route('admin.writing.prompts.show', $prompt) }}#samples" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 hover:bg-indigo-500/20 transition font-bold text-xs font-mono">
                                                <span>📝</span>
                                                <span>{{ $prompt->sampleEssays->count() }} bài mẫu</span>
                                            </a>
                                            @if ($prompt->suggested_outline)
                                                <span class="text-[10px] text-emerald-400 font-semibold inline-flex items-center gap-0.5">
                                                    <span>✓</span> Có dàn ý gợi ý
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status & Toggle -->
                                    <td class="py-4 px-4 text-center">
                                        <form method="POST" action="{{ route('admin.writing.prompts.toggle-status', $prompt) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="min-h-[32px] inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition cursor-pointer {{ $prompt->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' }}"
                                                title="Nhấn để đổi trạng thái"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full {{ $prompt->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400' }}"></span>
                                                {{ $prompt->status->label() }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap space-x-1.5">
                                        <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="min-h-[32px] inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition" title="Xem chi tiết">
                                            Chi tiết
                                        </a>
                                        <a href="{{ route('admin.writing.prompts.edit', $prompt) }}" class="min-h-[32px] inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition" title="Chỉnh sửa đề">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.writing.prompts.destroy', $prompt) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề bài Task 2 này? Các bài mẫu và bài nộp liên quan sẽ bị xóa.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="min-h-[32px] inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold border border-rose-500/20 transition cursor-pointer" title="Xóa đề">
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
                    {{ $prompts->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layouts.admin>
