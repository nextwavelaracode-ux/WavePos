@props(['pageTitle' => 'Page', 'parentTitle' => null, 'parentUrl' => '#'])

<div class="flex items-center gap-3 mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <div class="inline-flex shadow-sm -space-x-px me-2.5" role="group">
            <button type="button" onclick="window.history.back()" class="inline-flex items-center justify-center text-gray-600 dark:text-gray-400 bg-white dark:bg-neutral-900 rounded-s-lg box-border border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-800 hover:text-gray-800 dark:hover:text-white focus:z-10 focus:ring-2 focus:ring-brand-500 font-medium leading-5 w-8 h-8 focus:outline-none transition-colors">
                <svg class="w-4 h-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
            </button>
            <button type="button" onclick="window.history.forward()" class="inline-flex items-center justify-center text-gray-600 dark:text-gray-400 bg-white dark:bg-neutral-900 rounded-e-lg box-border border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-800 hover:text-gray-800 dark:hover:text-white focus:z-10 focus:ring-2 focus:ring-brand-500 font-medium leading-5 w-8 h-8 focus:outline-none transition-colors">
                <svg class="w-4 h-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            </button>
        </div>
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400 transition-colors">
                    <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
                    Inicio
                </a>
            </li>
            @if($parentTitle)
            <li>
                <div class="flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 rtl:rotate-180 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    <a href="{{ $parentUrl }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400 transition-colors">{{ $parentTitle }}</a>
                </div>
            </li>
            @endif
            <li aria-current="page">
                <div class="flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 rtl:rotate-180 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    <span class="inline-flex items-center text-sm font-semibold text-gray-800 dark:text-white/90">{{ $pageTitle }}</span>
                </div>
            </li>
        </ol>
    </nav>
</div>
