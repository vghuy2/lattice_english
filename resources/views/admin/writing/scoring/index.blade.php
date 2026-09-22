<x-layouts.admin title="Quản lý Rubrics & Luật chấm Writing">
    <div class="space-y-8 max-w-6xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Rubrics & Scoring Rules (Bộ luật Chấm Writing)
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    Cấu hình tiêu chí chấm theo IELTS Official Band Descriptors và các quy tắc chấm thuật toán cố định (Zero AI).
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Deterministic Scoring Engine: Active</span>
            </div>
        </div>

        <!-- Section 1: Band Descriptors (Rubrics) -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-white">
                        1. Tiêu chí chấm theo Band điểm (IELTS Band Descriptors)
                    </h2>
                    <p class="text-xs text-slate-400">Mô tả chuẩn năng lực được sử dụng để đưa ra nhận xét chi tiết sau khi chấm bài.</p>
                </div>

                <!-- Task Selector -->
                <div class="flex items-center bg-slate-800/90 p-1 rounded-2xl border border-slate-700">
                    @foreach ($taskTypes as $tt)
                        <a href="{{ route('admin.writing.scoring.index', ['task_type' => $tt->value, 'criterion' => $currentCriterion]) }}"
                           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $currentTaskType === $tt->value ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-400 hover:text-white' }}">
                            {{ $tt->label() }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Criteria Tabs -->
            <div class="flex flex-wrap gap-2 pt-1">
                @foreach ($criteria as $crit)
                    <a href="{{ route('admin.writing.scoring.index', ['task_type' => $currentTaskType, 'criterion' => $crit->value]) }}"
                       class="min-h-[40px] inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition {{ $currentCriterion === $crit->value ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-800 text-slate-300 hover:bg-slate-700 border border-slate-700' }}">
                        <span class="px-2 py-0.5 rounded-lg {{ $currentCriterion === $crit->value ? 'bg-white/20 text-white' : 'bg-slate-900 text-indigo-400' }} font-mono text-[11px]">
                            {{ $crit->shortCode() }}
                        </span>
                        <span>{{ $crit->label() }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Rubrics Grid for Selected Task & Criterion -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                @forelse ($rubrics as $rubric)
                    <div class="bg-slate-800/60 rounded-2xl border border-slate-700/60 p-5 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-700/80">
                                <span class="px-3 py-1 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 font-mono font-black text-sm">
                                    Band {{ number_format($rubric->band_score, 1) }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">
                                    {{ $rubric->criterion->shortCode() }}
                                </span>
                            </div>

                            <form method="POST" action="{{ route('admin.writing.scoring.rubrics.update', $rubric) }}" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <textarea
                                    name="description"
                                    rows="5"
                                    class="w-full p-3 rounded-xl border border-slate-700 text-xs text-slate-200 leading-relaxed bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >{{ $rubric->description }}</textarea>

                                <div class="flex justify-end">
                                    <button type="submit" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs transition cursor-pointer">
                                        Lưu mô tả
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-xs">
                        Chưa có dữ liệu rubric cho tiêu chí này. Hãy chạy seeder để khởi tạo.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Section 2: Deterministic Rule-Based Engine Configuration -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-white">
                        2. Bộ quy tắc Chấm điểm Cố định (Scoring Engine Rules)
                    </h2>
                    <p class="text-xs text-slate-400">Các quy tắc logic kiểm tra số từ, câu tổng quan, cấu trúc phân đoạn và độ phong phú từ vựng.</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($rules as $rule)
                    <div class="p-5 rounded-2xl border {{ $rule->is_active ? 'border-slate-700/80 bg-slate-800/70' : 'border-slate-800 bg-slate-900/50 opacity-60' }} shadow-2xs space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-md">
                                        {{ $rule->rule_key }}
                                    </span>
                                    <h3 class="font-bold text-sm text-white">{{ $rule->name }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-700 text-slate-300">
                                        {{ $rule->task_type === 'all' ? 'Task 1 & 2' : strtoupper($rule->task_type) }}
                                    </span>
                                    @if ($rule->criterion)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            {{ $rule->criterion->shortCode() }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400">{{ $rule->description }}</p>
                            </div>

                            <form method="POST" action="{{ route('admin.writing.scoring.rules.toggle', $rule) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="min-h-[36px] inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl font-bold text-xs border transition cursor-pointer {{ $rule->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' }}"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full {{ $rule->is_active ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                                    {{ $rule->is_active ? 'Đang kích hoạt' : 'Tạm tắt' }}
                                </button>
                            </form>
                        </div>

                        <!-- Parameters View & Edit -->
                        @if (!empty($rule->parameters))
                            <div class="pt-2 border-t border-slate-700/80">
                                <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider block mb-1">Tham số cấu hình (JSON):</span>
                                <form method="POST" action="{{ route('admin.writing.scoring.rules.update', $rule) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $rule->name }}">
                                    <input
                                        type="text"
                                        name="parameters"
                                        value="{{ json_encode($rule->parameters, JSON_UNESCAPED_UNICODE) }}"
                                        class="flex-1 px-3 py-2 rounded-xl border border-slate-700 text-xs font-mono bg-slate-900 text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                    <button type="submit" class="min-h-[36px] px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition cursor-pointer shrink-0">
                                        Cập nhật tham số
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-layouts.admin>
