<x-layouts.admin title="Quản lý Bài nộp Writing">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Quản lý Bài nộp IELTS Writing
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    Theo dõi toàn bộ bài viết của học viên, xem kết quả chấm điểm 4 tiêu chí và thực hiện chấm lại khi cập nhật luật.
                </p>
            </div>

            <a href="{{ route('admin.writing.scoring.index') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white font-bold text-xs shadow-md transition cursor-pointer">
                <span>⚙️ Cấu hình Luật chấm & Rubric</span>
            </a>
        </div>

        <!-- Top Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl font-bold">
                    📝
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng bài nộp</span>
                    <span class="text-2xl font-mono font-black text-white">{{ $totalSubmissions }}</span>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl font-bold">
                    ✅
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Đã chấm điểm</span>
                    <span class="text-2xl font-mono font-black text-emerald-400">{{ $gradedCount }}</span>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl font-bold">
                    ⭐
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Band TB Toàn sàn</span>
                    <span class="text-2xl font-mono font-black text-amber-400">{{ $avgScore ? "Band {$avgScore}" : '--' }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('admin.writing.submissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Tìm theo tên học viên, email hoặc tiêu đề đề bài..."
                        class="w-full min-h-[44px] px-3.5 rounded-2xl border border-slate-700 bg-slate-800 text-white placeholder:text-slate-500 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <!-- Task Type -->
                <div>
                    <select name="task_type" class="w-full min-h-[44px] px-3.5 rounded-2xl border border-slate-700 bg-slate-800 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="" class="bg-slate-900 text-slate-400">Tất cả dạng Task</option>
                        <option value="task_1" class="bg-slate-900 text-white" @selected($selectedTaskType === 'task_1')>Task 1 (Biểu đồ)</option>
                        <option value="task_2" class="bg-slate-900 text-white" @selected($selectedTaskType === 'task_2')>Task 2 (Nghị luận)</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <select name="status" class="w-full min-h-[44px] px-3.5 rounded-2xl border border-slate-700 bg-slate-800 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="" class="bg-slate-900 text-slate-400">Tất cả trạng thái</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" class="bg-slate-900 text-white" @selected($selectedStatus === $st->value)>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full min-h-[44px] px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition cursor-pointer">
                        Lọc kết quả
                    </button>
                </div>
            </form>
        </div>

        <!-- Submissions Table -->
        @if ($submissions->isEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-800 flex items-center justify-center text-2xl text-slate-500 mb-3">
                    📭
                </div>
                <h3 class="text-base font-bold text-white">Không tìm thấy bài nộp nào</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Chưa có bài viết nào phù hợp với bộ lọc hiện tại hoặc học viên chưa nộp bài.
                </p>
            </div>
        @else
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/60 border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-4 px-6">Học viên</th>
                                <th class="py-4 px-4">Đề bài & Task</th>
                                <th class="py-4 px-4">Số từ / Thời gian</th>
                                <th class="py-4 px-4">Ngày nộp</th>
                                <th class="py-4 px-4">4 Tiêu chí (TA-CC-LR-GRA)</th>
                                <th class="py-4 px-4">Band Score</th>
                                <th class="py-4 px-6 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-sm">
                            @foreach ($submissions as $sub)
                                @php $prompt = $sub->prompt; $user = $sub->user; @endphp
                                <tr class="hover:bg-slate-800/40 transition">
                                    
                                    <!-- Student -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-white block leading-tight">{{ $user?->name ?? 'Học viên ẩn danh' }}</span>
                                                <span class="text-[11px] text-slate-400 font-mono">{{ $user?->email }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Prompt -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $prompt?->task_type?->badgeClass() ?? 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                                {{ $prompt?->task_type?->label() ?? 'IELTS Writing' }}
                                            </span>
                                            <span class="font-bold text-slate-200 text-xs block line-clamp-1 max-w-xs">
                                                {{ $prompt?->title ?? 'Đề bài Writing' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Words & Time -->
                                    <td class="py-4 px-4 font-mono text-xs">
                                        <div class="font-bold text-white">{{ $sub->word_count }} từ</div>
                                        <div class="text-slate-400">{{ $sub->time_spent_formatted }}</div>
                                    </td>

                                    <!-- Date -->
                                    <td class="py-4 px-4 text-xs text-slate-400 font-mono">
                                        {{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : $sub->updated_at->format('d/m/Y H:i') }}
                                    </td>

                                    <!-- 4 Criteria -->
                                    <td class="py-4 px-4 font-mono text-xs text-slate-300">
                                        @if ($sub->isGraded())
                                            <span class="font-semibold text-white">{{ $sub->ta_score }}</span> /
                                            <span class="font-semibold text-white">{{ $sub->cc_score }}</span> /
                                            <span class="font-semibold text-white">{{ $sub->lr_score }}</span> /
                                            <span class="font-semibold text-white">{{ $sub->gra_score }}</span>
                                        @else
                                            <span class="text-slate-500">-- / -- / -- / --</span>
                                        @endif
                                    </td>

                                    <!-- Overall Band -->
                                    <td class="py-4 px-4">
                                        @if ($sub->isGraded())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                Band {{ number_format($sub->overall_score, 1) }}
                                            </span>
                                        @elseif ($sub->isDraft())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                                Bản nháp
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                Chờ chấm
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.writing.submissions.show', $sub) }}" class="min-h-[36px] inline-flex items-center px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs transition">
                                                Chi tiết
                                            </a>

                                            <form method="POST" action="{{ route('admin.writing.submissions.rescore', $sub) }}" onsubmit="return confirm('Bạn có chắc muốn chấm lại bài nộp này theo bộ luật mới nhất?')">
                                                @csrf
                                                <button type="submit" class="min-h-[36px] inline-flex items-center px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 font-bold text-xs transition cursor-pointer" title="Chấm lại tự động">
                                                    🔄 Chấm lại
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-800">
                    {{ $submissions->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layouts.admin>
