@props(['cuentasCobrar' => [], 'cuentasPagar' => []])

<div class="flex flex-col gap-4">

    {{-- Cuentas por Cobrar --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
                border-2 border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b-2 border-gray-100 dark:border-neutral-700">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                Cuentas por Cobrar
            </h3>
            <a href="{{ route('cuentas.index') }}"
               class="text-xs text-blue-500 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-neutral-800 max-h-52 overflow-y-auto">
            @forelse($cuentasCobrar as $c)
                <div class="flex items-center justify-between px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $c['cliente'] }}</p>
                        <p class="text-xs text-gray-400">Vence: {{ $c['vencimiento'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <p class="text-sm font-bold text-blue-600 dark:text-blue-400">${{ number_format($c['saldo']) }}</p>
                        @php
                            $badgeCobrar = match($c['estado']) {
                                'vencido'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'parcial'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'pendiente' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium {{ $badgeCobrar }}">
                            {{ ucfirst($c['estado']) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-400 text-center">Sin cuentas pendientes</p>
            @endforelse
        </div>
    </div>

    {{-- Cuentas por Pagar (compras a crédito) --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
                border-2 border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b-2 border-gray-100 dark:border-neutral-700">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Cuentas por Pagar
            </h3>
            <a href="{{ route('cuentas-por-pagar.index') }}"
               class="text-xs text-amber-500 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-neutral-800 max-h-52 overflow-y-auto">
            @forelse($cuentasPagar as $cp)
                <div class="flex items-center justify-between px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $cp['proveedor'] }}</p>
                        <p class="text-xs text-gray-400">{{ $cp['numero'] }} · Vence: {{ $cp['vencimiento'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <p class="text-sm font-bold text-amber-600 dark:text-amber-400">${{ number_format($cp['saldo']) }}</p>
                        @php
                            $badgePagar = match($cp['estado_pago']) {
                                'vencido'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'parcial'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'pendiente' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                default     => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium {{ $badgePagar }}">
                            {{ ucfirst($cp['estado_pago']) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-4 text-sm text-gray-400 text-center">Sin compras a crédito pendientes</p>
            @endforelse
        </div>
    </div>
</div>
