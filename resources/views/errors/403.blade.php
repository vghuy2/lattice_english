<x-layouts.guest title="403 - Không có quyền truy cập">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 sm:p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-2xl mx-auto mb-4">
            🚫
        </div>
        <h1 class="text-2xl font-bold text-slate-900">403 - Quyền truy cập bị từ chối</h1>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập vào khu vực này. Vui lòng quay lại hoặc liên hệ quản trị viên.' }}
        </p>
        <div class="mt-6">
            <a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                Quay về trang chủ
            </a>
        </div>
    </div>
</x-layouts.guest>
