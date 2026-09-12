@if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-800 dark:text-emerald-300 shadow-xs">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-200 cursor-pointer p-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if (session('status'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-blue-500/20 bg-blue-500/10 p-4 text-blue-800 dark:text-blue-300 shadow-xs">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 cursor-pointer p-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if (session('warning'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4 text-amber-800 dark:text-amber-300 shadow-xs">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-medium">{{ session('warning') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-amber-700 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-200 cursor-pointer p-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-800 dark:text-rose-300 shadow-xs">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 shrink-0 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-200 cursor-pointer p-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-rose-800 dark:text-rose-300 shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-semibold">Vui lòng kiểm tra lại các thông tin sau:</p>
            </div>
            <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-200 cursor-pointer p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 pl-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
