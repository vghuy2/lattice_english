<x-layouts.student title="Lịch sử Bài viết Writing">
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Sổ tay Bài viết & Lịch sử Nộp bài
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Theo dõi toàn bộ các bài viết Task 1 & Task 2 đã thực hiện, bản nháp đang viết dở và điểm số đánh giá.
                </p>
            </div>

            <a href="{{ route('student.writing.index') }}" class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                <span>+ Luyện viết đề mới</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 shadow-xs">
            <form method="GET" action="{{ route('student.writing.submissions.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <select name="task_type" class="w-full sm:w-48 min-h-[44px] px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="">Tất cả dạng Task</option>
                    <option value="task_1" @selected($selectedTaskType === 'task_1')>Task 1 (Biểu đồ)</option>
                    <option value="task_2" @selected($selectedTaskType === 'task_2')>Task 2 (Nghị luận)</option>
                </select>

                <select name="status" class="w-full sm:w-48 min-h-[44px] px-3.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" @selected($selectedStatus === $st->value)>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="min-h-[44px] px-5 py-2.5 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition w-full sm:w-auto">
                    Lọc
                </button>
            </form>
        </div>

        <!-- Submissions List -->
        @if ($submissions->isEmpty())
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 flex items-center justify-center text-2xl text-indigo-600 mb-3">
                    📝
                </div>
                <h3 class="text-base font-bold text-slate-900">Chưa có bài viết nào</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Hãy bắt đầu làm bài viết đầu tiên trong phòng luyện Writing để được theo dõi tiến độ và đánh giá điểm số.
                </p>
                <div class="mt-4">
                    <a href="{{ route('student.writing.index') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition">
                        Khám phá đề thi Writing
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Đề bài & Dạng Task</th>
                                <th class="py-3.5 px-4">Số từ</th>
                                <th class="py-3.5 px-4">Thời gian</th>
                                <th class="py-3.5 px-4">Ngày nộp / Cập nhật</th>
                                <th class="py-3.5 px-4">Trạng thái / Điểm</th>
                                <th class="py-3.5 px-6 text-right">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($submissions as $sub)
                                @php $prompt = $sub->prompt; @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    
                                    <!-- Title & Task -->
                                    <td class="py-4 px-6">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $prompt?->task_type?->badgeClass() ?? 'bg-slate-100 text-slate-700' }}">
                                                    {{ $prompt?->task_type?->label() ?? 'IELTS Writing' }}
                                                </span>
                                                <span class="text-xs text-slate-400">
                                                    {{ $prompt?->prompt_type?->shortLabel() }}
                                                </span>
                                            </div>

                                            <a href="{{ route('student.writing.submissions.show', $sub) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block">
                                                {{ $prompt?->title ?? 'Đề bài Writing' }}
                                            </a>
                                        </div>
                                    </td>

                                    <!-- Word Count -->
                                    <td class="py-4 px-4 font-mono font-bold text-slate-800">
                                        {{ $sub->word_count }} từ
                                    </td>

                                    <!-- Time Spent -->
                                    <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                        {{ $sub->time_spent_formatted }}
                                    </td>

                                    <!-- Date -->
                                    <td class="py-4 px-4 text-xs text-slate-500">
                                        {{ $sub->submitted_at ? $sub->submitted_at->format('d/m/Y H:i') : $sub->updated_at->format('d/m/Y H:i') }}
                                    </td>

                                    <!-- Status / Band Score -->
                                    <td class="py-4 px-4">
                                        @if ($sub->isGraded())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-black bg-emerald-100 text-emerald-800">
                                                Band {{ number_format($sub->overall_score, 1) }}
                                            </span>
                                        @elseif ($sub->isDraft())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                Bản nháp
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                Đã nộp bài
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="py-4 px-6 text-right">
                                        @if ($sub->isDraft() && $prompt)
                                            <a href="{{ route('student.writing.practice', $prompt) }}" class="min-h-[36px] inline-flex items-center px-3.5 py-1.5 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition shadow-xs">
                                                Viết tiếp →
                                            </a>
                                        @else
                                            <a href="{{ route('student.writing.submissions.show', $sub) }}" class="min-h-[36px] inline-flex items-center px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">
                                                Xem bài làm
                                            </a>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $submissions->links() }}
                </div>
            </div>
        @endif

    </div>
</x-layouts.student>
