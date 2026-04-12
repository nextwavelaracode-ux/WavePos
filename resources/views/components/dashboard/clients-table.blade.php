@props(['clientes' => []])

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700
             overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-neutral-800">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white">Clientes Principales</h3>
            <div class="flex items-center gap-1.5 mt-0.5">
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($clientes ?? []) }} adquiridos este mes
                </span>
            </div>
        </div>
        <a href="{{ route('clientes.index') }}"
           class="text-xs text-blue-500 hover:text-blue-700 dark:text-blue-400 font-medium transition-colors">
            Ver todos →
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[560px]">
            <thead>
                <tr class="bg-gray-50 dark:bg-neutral-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                        Cliente
                    </th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                        Total Compras
                    </th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                        Última Venta
                    </th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">
                        Nivel
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                @forelse($clientes as $cliente)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors group">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center
                                            bg-gradient-to-br from-blue-400 to-blue-600 text-white
                                            text-xs font-bold shadow-sm">
                                    {{ strtoupper(substr($cliente['nombre'] ?? 'C', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $cliente['nombre'] ?? '—' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $cliente['documento'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                ${{ number_format($cliente['total'] ?? 0) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $cliente['ultima_venta'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @php
                                $total = $cliente['total'] ?? 0;
                                if ($total >= 5000000) {
                                    $badge  = 'Platinum';
                                    $class  = 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
                                } elseif ($total >= 1000000) {
                                    $badge  = 'Gold';
                                    $class  = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                } else {
                                    $badge  = 'Regular';
                                    $class  = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $class }}">
                                {{ $badge }}
                            </span>
                        </td>
                    </tr>
                @empty
                    {{-- Empty state with placeholder rows --}}
                    @foreach([
                        ['nombre' => 'Addidis Sportwear',    'total' => 14000000,  'doc' => 'NIT 900.123.456', 'fecha' => 'Hoy 8:10 PM'],
                        ['nombre' => 'Netflixer Platforms',  'total' => 30000000,  'doc' => 'NIT 901.234.567', 'fecha' => 'Ayer 11 PM'],
                        ['nombre' => 'Shopifi Stores',       'total' => 8500000,   'doc' => 'NIT 900.876.543', 'fecha' => '11 Jul'],
                        ['nombre' => 'Tailwind Technologies','total' => 20500000,  'doc' => 'NIT 900.654.321', 'fecha' => '10 Jul'],
                        ['nombre' => 'Community First',      'total' => 9800000,   'doc' => 'NIT 901.111.222', 'fecha' => '9 Jul'],
                    ] as $placeholder)
                        <tr class="hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center
                                                bg-gradient-to-br from-blue-400 to-blue-600 text-white
                                                text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($placeholder['nombre'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $placeholder['nombre'] }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $placeholder['doc'] }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    ${{ number_format($placeholder['total']) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $placeholder['fecha'] }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    if ($placeholder['total'] >= 20000000) {
                                        $badge = 'Platinum'; $cls = 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
                                    } elseif ($placeholder['total'] >= 10000000) {
                                        $badge = 'Gold'; $cls = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                                    } else {
                                        $badge = 'Regular'; $cls = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">
                                    {{ $badge }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>
    </div>
</div>
