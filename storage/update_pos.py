import re

with open('c:/Users/jjdia/Downloads/NextWave/WavePos/resources/views/pages/caja/pos.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update posApp state to include viewMode
content = re.sub(
    r'(mobileTab: \'productos\',)',
    r"viewMode: 'cards',\n                \1",
    content
)

# 2. Add Switcher to the Filter Bar
# Find: <div class="p-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex flex-wrap gap-2 items-center">
switcher_component = """                <div class="p-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex flex-wrap gap-2 items-center">
                    <!-- Mode Switcher -->
                    <div class="relative flex gap-1 bg-zinc-100 dark:bg-zinc-800/50 p-1 border border-zinc-200 dark:border-white/10 rounded-lg hidden sm:flex shrink-0">
                        <div class="absolute top-1 left-1 bottom-1 w-8 rounded-md bg-white dark:bg-zinc-700 shadow-sm transition-transform duration-300 ease-out"
                            :class="{'translate-x-0': viewMode === 'cards', 'translate-x-[36px]': viewMode === 'stack'}"></div>
                        <button @click="viewMode = 'cards'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'cards' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Cuadrícula">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </button>
                        <button @click="viewMode = 'stack'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'stack' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Lista">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        </button>
                    </div>"""

content = content.replace(
    '<div class="p-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex flex-wrap gap-2 items-center">',
    switcher_component
)

# 3. Update the Product Grid section
grid_match = re.search(r'\{\{-- Product Grid --\}\}.*?</div>\s*</div>', content, re.DOTALL)
if grid_match:
    new_grid = """{{-- Product Grid --}}
                <div class="flex-1 overflow-y-auto p-3">
                    <div class="pos-grid grid gap-3" :class="viewMode === 'cards' ? 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5' : 'grid-cols-1 md:grid-cols-2'">
                        @foreach ($productos as $producto)
                            <div x-show="productoVisible({{ $producto->id }}, {{ $producto->categoria_id ?? 'null' }})"
                                @click="agregarProductoMobile({{ $producto->id }})"
                                class="group cursor-pointer rounded-xl border border-gray-200 bg-white hover:border-brand-400 hover:shadow-md transition-all duration-200 dark:border-white/[0.05] dark:bg-[#1e1e1e] dark:hover:border-brand-500/50 overflow-hidden"
                                :class="viewMode === 'cards' ? 'pos-product-card flex flex-col' : 'flex flex-row items-center p-3 gap-4 hover:bg-brand-50/20 dark:hover:bg-white/[0.02]'"
                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                data-precio="{{ $producto->precio_venta }}" data-impuesto="{{ $producto->impuesto }}"
                                data-stock="{{ $producto->stock }}" data-codigo="{{ $producto->codigo_barras }}"
                                data-categoria="{{ $producto->categoria_id }}">
                                
                                <div :class="viewMode === 'cards' ? 'pos-product-placeholder h-24 w-full bg-gray-100 dark:bg-gray-800' : 'h-14 w-14 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden'">
                                    @if ($producto->imagen_url)
                                        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                            class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <svg class="w-1/2 h-1/2 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div :class="viewMode === 'cards' ? 'p-2' : 'flex-1 min-w-0 flex items-center justify-between'">
                                    <div :class="viewMode === 'cards' ? '' : 'flex-1 pr-2'">
                                        <p class="pos-product-name font-bold text-gray-800 dark:text-white/90 leading-tight truncate" :class="viewMode === 'cards' ? 'text-xs' : 'text-sm'">
                                            {{ $producto->nombre }}
                                        </p>
                                        <template x-if="viewMode === 'stack'">
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">{{ optional($producto->categoria)->nombre ?? 'Base' }}</span>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div :class="viewMode === 'cards' ? 'mt-0.5' : 'text-right'">
                                        <p class="pos-product-price font-bold text-brand-600 dark:text-brand-400" :class="viewMode === 'cards' ? 'text-sm' : 'text-base'">
                                            ${{ number_format($producto->precio_venta, 2) }}
                                        </p>
                                        <p class="pos-product-stock text-xs text-gray-400 font-medium">Stock: {{ $producto->stock }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>"""
    content = content[:grid_match.start()] + new_grid + content[grid_match.end():]

with open('c:/Users/jjdia/Downloads/NextWave/WavePos/resources/views/pages/caja/pos.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated pos.blade.php")
