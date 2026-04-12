@php
    use App\Helpers\MenuHelper;
    $currentPath = request()->path();

    $navGroups = [
        [
            'label'  => 'Caja & Ventas',
            'icon'   => 'pos',
            'items'  => MenuHelper::filterMenuItems(MenuHelper::getCajaNavItems()),
        ],
        [
            'label'  => 'Inventario',
            'icon'   => 'productos',
            'items'  => MenuHelper::filterMenuItems(MenuHelper::getInventarioNavItems()),
        ],
        [
            'label'  => 'Compras & Gastos',
            'icon'   => 'compras',
            'items'  => MenuHelper::filterMenuItems(array_merge(
                MenuHelper::getComprasNavItems(),
                MenuHelper::getGastosNavItems()
            )),
        ],
        [
            'label'  => 'Facturación DIAN',
            'icon'   => 'facturacion',
            'items'  => MenuHelper::filterMenuItems(MenuHelper::getFacturacionNavItems()),
        ],
        [
            'label'  => 'Clientes',
            'icon'   => 'clientes',
            'items'  => MenuHelper::filterMenuItems(MenuHelper::getClientesNavItems()),
        ],
        [
            'label'  => 'Configuración',
            'icon'   => 'configuracion-sistema',
            'items'  => MenuHelper::filterMenuItems(MenuHelper::getPosNavItems()),
        ],
    ];

    // Remove groups with 0 items
    $navGroups = array_values(array_filter($navGroups, fn($g) => count($g['items']) > 0));

    // Top-level "menu" items (Dashboard, Profile)
    $mainItems = MenuHelper::filterMenuItems(MenuHelper::getMainNavItems());
@endphp

{{-- ==========  OVERLAY (Mobile)  ========== --}}
<div
    x-show="$store.sidebar.isMobileOpen"
    @click="$store.sidebar.setMobileOpen(false)"
    class="fixed inset-0 z-[998] bg-black/40 backdrop-blur-sm lg:hidden"
    style="display:none;"
></div>

{{-- ==========  SIDEBAR  ========== --}}
<aside
    id="sidebar"
    class="fixed top-0 left-0 h-screen w-[260px] z-[999] flex flex-col
           bg-neutral-50 dark:bg-neutral-900
           border-r border-neutral-200 dark:border-neutral-800
           transition-transform duration-300 ease-in-out"
    :class="{
        'translate-x-0'    : $store.sidebar.isExpanded || $store.sidebar.isMobileOpen,
        '-translate-x-full': !$store.sidebar.isExpanded && !$store.sidebar.isMobileOpen
    }"
    style="will-change: transform;"
    x-data="{
        openGroup: null,
        isActive(path) {
            return window.location.pathname === path
                || window.location.pathname.startsWith(path + '/');
        },
        currentGroupContains(items) {
            return items.some(i => this.isActive(i.path));
        },
        init() {
            {{-- Auto-open group that contains the active link --}}
            @foreach($navGroups as $gi => $group)
                @if(count($group['items']))
                    if (this.currentGroupContains({{ json_encode(collect($group['items'])->map(fn($i) => ['path' => $i['path']])->values()) }})) {
                        this.openGroup = {{ $gi }};
                    }
                @endif
            @endforeach
        }
    }"
>

    {{-- ── HEADER (Logo) ──────────────────────────────────────── --}}
    <div class="flex h-14 shrink-0 items-center px-4 border-b border-neutral-200 dark:border-neutral-800">
        <a href="/" class="flex items-center gap-2.5 overflow-hidden min-w-0">
            <img src="/images/logo/wavepos-icon.svg" alt="WavePOS" class="h-8 w-8 shrink-0"/>
            <span class="font-semibold text-sm text-neutral-900 dark:text-white whitespace-nowrap">WavePOS</span>
        </a>
    </div>

    {{-- ── NAVIGATION ──────────────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-0.5 no-scrollbar text-neutral-700 dark:text-neutral-200">

        {{-- Top-level items (Dashboard, Profile) --}}
        @foreach($mainItems as $item)
            @if(isset($item['subItems']))
                {{-- Dashboard with sub-items → render as flat items --}}
                @foreach($item['subItems'] as $sub)
                    <a
                        href="{{ $sub['path'] }}"
                        title="{{ $sub['name'] }}"
                        class="group flex items-center gap-2.5 rounded-lg px-2 py-2 text-sm font-medium
                               transition-colors relative overflow-hidden
                               {{ request()->is(ltrim($sub['path'], '/')) || request()->is(ltrim($sub['path'], '/').'/*')
                                    ? 'bg-primary-500/10 dark:bg-primary-500/15 text-primary-600 dark:text-primary-400'
                                    : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 h-5 w-5 flex items-center justify-center">
                            {!! MenuHelper::getIconSvg($item['icon']) !!}
                        </span>
                        <span
                            class="truncate transition-all duration-200 whitespace-nowrap"
                            :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100 max-w-[160px]' : 'opacity-0 max-w-0 absolute'"
                        >{{ $sub['name'] }}</span>
                    </a>
                @endforeach
            @else
                <a
                    href="{{ $item['path'] }}"
                    title="{{ $item['name'] }}"
                    class="group flex items-center gap-2.5 rounded-lg px-2 py-2 text-sm font-medium
                           transition-colors relative overflow-hidden
                           {{ request()->is(ltrim($item['path'], '/'))
                                ? 'bg-primary-500/10 dark:bg-primary-500/15 text-primary-600 dark:text-primary-400'
                                : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white' }}"
                >
                    <span class="shrink-0 h-5 w-5 flex items-center justify-center">
                        {!! MenuHelper::getIconSvg($item['icon']) !!}
                    </span>
                    <span
                        class="truncate transition-all duration-200 whitespace-nowrap"
                        :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100 max-w-[160px]' : 'opacity-0 max-w-0 absolute'"
                    >{{ $item['name'] }}</span>
                </a>
            @endif
        @endforeach

        {{-- Divider --}}
        <div class="my-2 border-t border-neutral-200 dark:border-neutral-800"></div>

        {{-- Grouped navigation --}}
        @foreach($navGroups as $gi => $group)
            @if(count($group['items']) === 1)
                {{-- Single item group → render directly --}}
                @php $item = $group['items'][0]; @endphp
                <a
                    href="{{ $item['path'] }}"
                    title="{{ $item['name'] }}"
                    class="group flex items-center gap-2.5 rounded-lg px-2 py-2 text-sm font-medium
                           transition-colors relative overflow-hidden
                           {{ request()->is(ltrim($item['path'], '/')) || request()->is(ltrim($item['path'],'/').'/*')
                                ? 'bg-primary-500/10 dark:bg-primary-500/15 text-primary-600 dark:text-primary-400'
                                : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white' }}"
                >
                    <span class="shrink-0 h-5 w-5 flex items-center justify-center">
                        {!! MenuHelper::getIconSvg($group['icon']) !!}
                    </span>
                    <span
                        class="truncate whitespace-nowrap transition-all duration-200"
                        :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100 max-w-[160px]' : 'opacity-0 max-w-0 absolute'"
                    >{{ $item['name'] }}</span>
                </a>
            @else
                {{-- Multi-item group → collapsible --}}
                <div>
                    {{-- Group trigger --}}
                    <button
                        @click="($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen) && (openGroup = openGroup === {{ $gi }} ? null : {{ $gi }})"
                        title="{{ $group['label'] }}"
                        class="group w-full flex items-center gap-2.5 rounded-lg px-2 py-2 text-sm font-medium
                               transition-colors relative overflow-hidden
                               {{ collect($group['items'])->contains(fn($i) => request()->is(ltrim($i['path'],'/')) || request()->is(ltrim($i['path'],'/').'/*'))
                                    ? 'text-primary-600 dark:text-primary-400'
                                    : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 hover:text-neutral-900 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 h-5 w-5 flex items-center justify-center">
                            {!! MenuHelper::getIconSvg($group['icon']) !!}
                        </span>
                        <span
                            class="flex-1 text-left truncate whitespace-nowrap transition-all duration-200"
                            :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100 max-w-[130px]' : 'opacity-0 max-w-0 absolute'"
                        >{{ $group['label'] }}</span>
                        <svg
                            class="ml-auto h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                            :class="{
                                'rotate-90' : openGroup === {{ $gi }},
                                'hidden'    : !($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)
                            }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Sub-items --}}
                    <div
                        x-show="openGroup === {{ $gi }} && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-0.5 ml-4 pl-2 border-l border-neutral-200 dark:border-neutral-800 space-y-0.5"
                        style="display:none;"
                    >
                        @foreach($group['items'] as $item)
                            <a
                                href="{{ $item['path'] }}"
                                class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm
                                       transition-colors
                                       {{ request()->is(ltrim($item['path'],'/')) || request()->is(ltrim($item['path'],'/').'/*')
                                            ? 'text-primary-600 dark:text-primary-400 font-medium bg-primary-500/8 dark:bg-primary-500/10'
                                            : 'text-neutral-500 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-200/50 dark:hover:bg-neutral-800' }}"
                            >
                                {{ $item['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- ── USER PROFILE ────────────────────────────────────────── --}}
    <div class="shrink-0 border-t border-neutral-200 dark:border-neutral-800 p-2"
         x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
        <button
            @click="($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen) && (profileOpen = !profileOpen)"
            class="w-full flex items-center gap-2.5 rounded-lg px-2 py-2
                   text-neutral-600 dark:text-neutral-300
                   hover:bg-neutral-200/60 dark:hover:bg-neutral-800
                   hover:text-neutral-900 dark:hover:text-white
                   transition-colors"
        >
            {{-- Avatar --}}
            <div class="h-8 w-8 shrink-0 rounded-full overflow-hidden bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-semibold text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div
                class="flex-1 text-left min-w-0 transition-all duration-200"
                :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
            >
                <p class="text-sm font-medium text-neutral-900 dark:text-white truncate leading-tight">{{ auth()->user()->name ?? 'Usuario' }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-400 truncate leading-tight">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <svg
                class="h-4 w-4 shrink-0 text-neutral-400 transition-all duration-200"
                :class="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen ? 'opacity-100' : 'opacity-0 w-0'"
                fill="none" viewBox="0 0 24 24" stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
            </svg>
        </button>

        {{-- Profile dropdown --}}
        <div
            x-show="profileOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute bottom-16 left-2 right-2 z-50 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl shadow-neutral-900/10 dark:shadow-black/30 p-1"
            style="display:none;"
        >
            <a href="/profile" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mi Perfil
            </a>
            <div class="my-1 border-t border-neutral-100 dark:border-neutral-800"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</aside>
