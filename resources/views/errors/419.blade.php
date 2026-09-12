<x-layouts.guest title="419 - Phiên làm việc hết hạn">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 sm:p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-2xl mx-auto mb-4">
            ⏳
        </div>
        <h1 class="text-2xl font-bold text-slate-900">419 - Hết hạn phiên làm việc</h1>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Phiên bảo mật của bạn đã hết hạn do không có tương tác trong một thời gian. Vui lòng làm mới trang hoặc đăng nhập lại.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="javascript:location.reload()" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Làm mới trang
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                Đăng nhập lại
            </a>
        </div>
    </div>
</x-layouts.guest>
