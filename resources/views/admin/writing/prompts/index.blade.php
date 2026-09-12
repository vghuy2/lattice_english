<x-layouts.admin title="Quản lý Đề bài IELTS Writing">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Quản lý Đề bài IELTS Writing
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Quản lý ngân hàng đề Task 1 & Task 2, dàn ý gợi ý, bài mẫu chuẩn band điểm và ảnh biểu đồ.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.writing.prompts.create') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Thêm Đề bài Mới</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.writing.prompts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm tiêu đề hoặc nội dung đề thi..."
                        class="w-full min-h-[44px] px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"
                    >
                </div>

                <!-- Task Type Filter -->
                <div>
                    <select name="task_type" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <option value="">Tất cả dạng Task</option>
                        @foreach ($taskTypes as $tt)
                            <option value="{{ $tt->value }}" @selected($selectedTaskType === $tt->value)>
                                {{ $tt->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Filter -->
                <div>
                    <select name="level" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
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
                    <select name="status" class="w-full min-h-[44px] px-3 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" @selected($selectedStatus === $st->value)>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="min-h-[44px] px-4 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition shrink-0">
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Prompts Table / Cards -->
        @if ($prompts->isEmpty())
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 flex items-center justify-center text-2xl text-indigo-600 mb-3">
                    ✍️
                </div>
                <h3 class="text-base font-bold text-slate-900">Không tìm thấy đề bài Writing nào</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Hãy tạo đề bài mới để học viên có tài liệu luyện tập Task 1 và Task 2 chuẩn format IELTS.
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.writing.prompts.create') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition">
                        Tạo đề bài đầu tiên
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Dạng bài & Tiêu đề</th>
                                <th class="py-3.5 px-4">Dạng biểu đồ / Nghị luận</th>
                                <th class="py-3.5 px-4">Trình độ</th>
                                <th class="py-3.5 px-4">Bài mẫu</th>
                                <th class="py-3.5 px-4">Trạng thái</th>
                                <th class="py-3.5 px-6 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($prompts as $prompt)
                                <tr class="hover:bg-slate-50/70 transition">
                                    
                                    <!-- Title & Task Type -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-start gap-3">
                                            @if ($prompt->image_path)
                                                <img src="{{ $prompt->image_url }}" alt="Chart" class="w-12 h-12 object-cover rounded-xl border border-slate-200 shrink-0 bg-slate-100">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-bold text-indigo-600 text-sm shrink-0">
                                                    {{ $prompt->task_type->shortLabel() }}
                                                </div>
                                            @endif

                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $prompt->task_type->badgeClass() }}">
                                                        {{ $prompt->task_type->label() }}
                                                    </span>
                                                    @if ($prompt->topic)
                                                        <span class="text-xs text-slate-500 font-medium">
                                                            {{ $prompt->topic->icon ?? '📁' }} {{ $prompt->topic->title }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                                    {{ $prompt->title }}
                                                </a>

                                                <p class="text-xs text-slate-500 line-clamp-1 max-w-md">
                                                    {{ $prompt->prompt_text }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prompt Type -->
                                    <td class="py-4 px-4 text-xs font-semibold text-slate-700">
                                        {{ $prompt->prompt_type->label() }}
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

                                    <!-- Sample Essays Count -->
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs font-mono">
                                            📝 {{ $prompt->sampleEssays->count() }} bài
                                        </span>
                                    </td>

                                    <!-- Status & Toggle -->
                                    <td class="py-4 px-4">
                                        <form method="POST" action="{{ route('admin.writing.prompts.toggle-status', $prompt) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="min-h-[32px] px-3 py-1 rounded-full text-xs font-bold border transition cursor-pointer {{ $prompt->status === \App\Enums\ContentStatus::PUBLISHED ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}"
                                                title="Nhấn để đổi trạng thái"
                                            >
                                                {{ $prompt->status->label() }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.writing.prompts.show', $prompt) }}" class="min-h-[36px] min-w-[36px] inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Xem chi tiết & Bài mẫu">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            <a href="{{ route('admin.writing.prompts.edit', $prompt) }}" class="min-h-[36px] min-w-[36px] inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-amber-600 hover:bg-amber-50 transition" title="Chỉnh sửa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <form method="POST" action="{{ route('admin.writing.prompts.destroy', $prompt) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề bài này và toàn bộ bài mẫu đính kèm?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="min-h-[36px] min-w-[36px] inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Xóa đề bài">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $prompts->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layouts.admin>
