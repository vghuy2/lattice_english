<x-layouts.guest title="500 - Lỗi máy chủ">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 sm:p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-2xl mx-auto mb-4">
            ⚠️
        </div>
        <h1 class="text-2xl font-bold text-slate-900">500 - Lỗi hệ thống</h1>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Hệ thống đang gặp sự cố tạm thời khi xử lý yêu cầu của bạn. Đội ngũ kỹ thuật đã được thông báo.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="javascript:location.reload()" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                Thử tải lại trang
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                Quay về trang chủ
            </a>
        </div>
    </div>
</x-layouts.guest>
