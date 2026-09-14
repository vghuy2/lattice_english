<x-layouts.guest title="Lattice IELTS — Nền Tảng Luyện Viết & Chấm Điểm Chuẩn Hóa">
    <div class="text-center space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-xs font-semibold text-indigo-700">
            <span>✨ Chuẩn hóa Band 4.5 – 5.0 lên Band 6.0 – 6.5+</span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
            Luyện Viết & Học Từ Vựng IELTS Chuyên Sâu
        </h1>

        <p class="text-slate-600 text-sm leading-relaxed">
            Hệ thống phòng thi Writing Lab chia đôi màn hình kết hợp bộ máy chấm điểm định lượng quy tắc 100% PHP thuần minh bạch và lặp lại ngắt quãng Spaced Repetition.
        </p>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition">
                Đăng nhập vào học tập →
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50 transition">
                Tạo tài khoản mới
            </a>
        </div>

        <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-2 text-center">
            <div class="p-2">
                <span class="font-extrabold text-indigo-700 text-lg block">4+</span>
                <span class="text-[11px] text-slate-500 font-medium">Chủ đề từ vựng</span>
            </div>
            <div class="p-2">
                <span class="font-extrabold text-indigo-700 text-lg block">ZERO AI</span>
                <span class="text-[11px] text-slate-500 font-medium">Chấm điểm quy tắc</span>
            </div>
            <div class="p-2">
                <span class="font-extrabold text-indigo-700 text-lg block">4 Tiêu chí</span>
                <span class="text-[11px] text-slate-500 font-medium">TA, CC, LR, GRA</span>
            </div>
        </div>
    </div>
</x-layouts.guest>
