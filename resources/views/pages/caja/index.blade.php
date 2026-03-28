@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-2xl md:p-6" x-data="{ tab: 'movimientos', modalAbrir: false, modalCerrar: false }">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Caja Registradora</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gestiona la apertura, cierre y movimiento de caja</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @if($cajaAbierta)
                <a href="{{ route('caja.pos') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    POS
                </a>
                <button @click="modalCerrar = true" class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-600 transition">
                    Cerrar Caja
                </button>
            @else
                <button @click="modalAbrir = true" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 transition">
                    Abrir Caja
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400 flex justify-between">
            <span>{{ session('success') }}</span>
            <button @click="show = false">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 flex justify-between">
            <span>{{ session('error') }}</span>
            <button @click="show = false">&times;</button>
        </div>
    @endif

    @if($cajaAbierta)
    {{-- === CAJA ABIERTA === --}}
    
    {{-- 4 Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($montoEsperado, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Efectivo en Caja</span>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($totalTarjeta, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Ventas con Tarjeta</span>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
                     <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($totalTransferencia, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Transferencias</span>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-500 dark:bg-purple-500/10 dark:text-purple-400">
                    <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-brand-600 dark:text-brand-400">${{ number_format($totalVentas, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Total Ventas</span>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navbar --}}
    <div class="mb-6 flex flex-wrap gap-3 border-b border-gray-200 pb-3 dark:border-gray-800">
        <button @click="tab = 'movimientos'" :class="tab === 'movimientos' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm transition">
            Movimientos del Turno
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
        </button>
        <button @click="tab = 'arqueo'" :class="tab === 'arqueo' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm transition">
            Arqueo
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
        </button>
        <button @click="tab = 'turnos'" :class="tab === 'turnos' ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm transition">
            Historial de Turnos
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </button>
    </div>

    {{-- Content Sections --}}
    
    <!-- MOVIMIENTOS -->
    <div x-show="tab === 'movimientos'" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Hora</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Concepto</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Métodos</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Usuario</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <!-- Fondo inicial -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-gray-500">{{ $cajaAbierta->fecha_apertura->format('H:i') }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">Fondo Inicial Apertura</td>
                        <td class="px-4 py-3 text-gray-500">Efectivo</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cajaAbierta->usuario->name ?? 'Admin' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">+${{ number_format($cajaAbierta->monto_inicial, 2) }}</td>
                    </tr>
                    <!-- Ventas -->
                    @forelse($movimientosTurno as $venta)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-gray-500">{{ $venta->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3 text-gray-800 dark:text-gray-200">Venta <span class="text-xs text-gray-400">#{{ $venta->numero }}</span></td>
                        <td class="px-4 py-3 text-gray-500">
                            @foreach($venta->pagos->pluck('metodo')->unique() as $met)
                                <span class="capitalize">{{ $met }}</span>@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $venta->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-gray-200">+${{ number_format($venta->total, 2) }}</td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ARQUEO -->
    <div x-show="tab === 'arqueo'" class="grid gap-6 xl:grid-cols-3" x-cloak 
         x-data="arqueoData({{ $montoEsperado }})">
        <!-- Calculadora Izquierda -->
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Conteo Físico</h3>
            <div class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                <!-- Monedas -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Monedas (PAB / USD)</h4>
                    <template x-for="(moneda, key) in monedas" :key="key">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm text-gray-600 dark:text-gray-300 w-24 whitespace-nowrap" x-text="moneda.label"></label>
                            <input type="number" min="0" x-model.number="moneda.qty" @input="calcular()" class="h-9 w-24 rounded-lg border border-gray-300 bg-white px-2 py-1 text-right text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="0">
                            <span class="w-16 text-right font-mono text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="'$' + (moneda.qty * moneda.val).toFixed(2)"></span>
                        </div>
                    </template>
                </div>
                <!-- Billetes -->
                <div>
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 dark:border-gray-800 pb-2">Billetes (USD)</h4>
                    <template x-for="(billete, key) in billetes" :key="key">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm text-gray-600 dark:text-gray-300 w-24 whitespace-nowrap" x-text="'Billete $' + billete.val"></label>
                            <input type="number" min="0" x-model.number="billete.qty" @input="calcular()" class="h-9 w-24 rounded-lg border border-gray-300 bg-white px-2 py-1 text-right text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="0">
                            <span class="w-16 text-right font-mono text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="'$' + (billete.qty * billete.val).toFixed(2)"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button @click="resetear()" class="text-sm text-red-500 hover:text-red-600">Restablecer contadores</button>
            </div>
        </div>

        <!-- Resumen Derecha -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Resumen de Arqueo</h3>
            
            <div class="space-y-4 flex-grow">
                <div class="flex justify-between items-center rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <span class="text-sm text-gray-500">Efectivo Esperado</span>
                    <span class="font-bold text-gray-800 dark:text-white text-lg">${{ number_format($montoEsperado, 2) }}</span>
                </div>
                
                <div class="flex justify-between items-center rounded-lg bg-emerald-50 p-3 dark:bg-emerald-900/10">
                    <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Efectivo Contado</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-lg" x-text="'$' + totalContado.toFixed(2)"></span>
                </div>

                <div class="flex justify-between items-center rounded-lg p-3" :class="diferencia > 0 ? 'bg-emerald-50 dark:bg-emerald-900/10' : (diferencia < 0 ? 'bg-red-50 dark:bg-red-900/10' : 'bg-brand-50 dark:bg-brand-900/10')">
                    <span class="text-sm font-medium" :class="diferencia > 0 ? 'text-emerald-600' : (diferencia < 0 ? 'text-red-600' : 'text-brand-600')">Diferencia</span>
                    <span class="font-bold text-xl" :class="diferencia > 0 ? 'text-emerald-600' : (diferencia < 0 ? 'text-red-600' : 'text-brand-600')" x-text="(diferencia > 0 ? '+' : '') + '$' + diferencia.toFixed(2)"></span>
                </div>
            </div>

            <!-- Alerta Tiempo Real -->
            <div class="mt-6 rounded-lg border p-4 text-center" :class="estadoClase">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full" :class="estadoIconBg">
                    <svg class="h-6 w-6" :class="estadoIconText" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-html="estadoIconPath"></svg>
                </div>
                <h5 class="text-base font-bold" :class="estadoTextTitle" x-text="estadoMensaje"></h5>
                <p class="mt-1 text-xs" :class="estadoTextDesc" x-text="estadoDesc"></p>
            </div>
            
            <button @click="modalCerrar = true" class="mt-4 w-full rounded-lg bg-red-500 px-4 py-3 text-sm font-semibold text-white hover:bg-red-600 transition">
                Ir a Cerrar Caja
            </button>
        </div>
    </div>

    <!-- HISTORIAL TURNOS -->
    <div x-show="tab === 'turnos'" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Fecha</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Cajero</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Apertura</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Cierre</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Fondo Inicial</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Total Ventas</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Diferencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($historialCajas as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $c->fecha_apertura->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $c->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $c->fecha_apertura->format('H:i') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ optional($c->fecha_cierre)->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-500">${{ number_format($c->monto_inicial, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white">${{ number_format($c->total_ventas, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($c->diferencia > 0)
                                <span class="text-emerald-600 font-medium">+${{ number_format($c->diferencia, 2) }}</span>
                            @elseif($c->diferencia < 0)
                                <span class="text-red-500 font-medium">${{ number_format($c->diferencia, 2) }}</span>
                            @else
                                <span class="text-gray-400">$0.00</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay turnos anteriores.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else

    {{-- === CAJA CERRADA === --}}
    <div class="flex min-h-[50vh] flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">
            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h3 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">La caja está cerrada</h3>
        <p class="mb-6 text-center text-gray-500 max-w-sm">Para registrar ventas, procesar devoluciones o revisar el arqueo en tiempo real, debes iniciar un turno.</p>
        <button @click="modalAbrir = true" class="rounded-lg bg-emerald-500 px-6 py-3 font-semibold text-white hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/30">
            Abrir Caja Ahora
        </button>
    </div>

    <!-- HISTORIAL TURNOS (Cuando está cerrada también podemos verlo) -->
    <div class="mt-8 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Últimos Turnos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Fecha</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Cajero</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Apertura</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Cierre</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Fondo Inicial</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Total Ventas</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Diferencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($historialCajas as $c)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $c->fecha_apertura->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $c->usuario->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $c->fecha_apertura->format('H:i') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ optional($c->fecha_cierre)->format('H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-500">${{ number_format($c->monto_inicial, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white">${{ number_format($c->total_ventas, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($c->diferencia > 0)
                                <span class="text-emerald-600 font-medium">+${{ number_format($c->diferencia, 2) }}</span>
                            @elseif($c->diferencia < 0)
                                <span class="text-red-500 font-medium">${{ number_format($c->diferencia, 2) }}</span>
                            @else
                                <span class="text-gray-400">$0.00</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay turnos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- MODAL ABRIR CAJA --}}
    <div x-show="modalAbrir" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" x-cloak>
        <div @click.outside="modalAbrir = false" class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900 shadow-2xl">
            <h3 class="mb-4 text-xl font-bold text-gray-800 dark:text-white">Apertura de Turno</h3>
            <form action="{{ route('caja.abrir') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sucursal *</label>
                    <select name="sucursal_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">Seleccionar...</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fondo Inicial ($) *</label>
                    <input type="number" name="monto_inicial" step="0.01" min="0" required value="0.00" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="modalAbrir = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600">Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CERRAR CAJA --}}
    @if($cajaAbierta)
    <div x-show="modalCerrar" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" x-cloak>
        <div @click.outside="modalCerrar = false" class="w-full max-w-lg rounded-2xl bg-white p-6 dark:bg-gray-900 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Cerrar Caja</h3>
            <p class="text-sm text-gray-500 mb-6">Confirma el cierre de caja para finalizar turno. Asegúrate de verificar el efectivo contabilizado.</p>
            
            <div class="mb-6 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <div class="text-gray-500">Monto Inicial:</div>
                    <div class="text-right font-medium text-gray-800 dark:text-white">${{ number_format($cajaAbierta->monto_inicial, 2) }}</div>
                    
                    <div class="text-gray-500">Ventas (Tarjeta/Trans):</div>
                    <div class="text-right font-medium text-gray-800 dark:text-white">${{ number_format($totalTarjeta + $totalTransferencia, 2) }}</div>
                    
                    <div class="text-gray-500 font-bold mt-2">Efectivo Esperado:</div>
                    <div class="text-right font-bold text-gray-800 dark:text-white mt-2">${{ number_format($montoEsperado, 2) }}</div>
                </div>
            </div>

            <form action="{{ route('caja.cerrar') }}" method="POST">
                @csrf
                <input type="hidden" name="caja_id" value="{{ $cajaAbierta->id }}">
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Efectivo Físico Contado ($) *</label>
                    <input type="number" name="monto_real_cierre" step="0.01" min="0" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm font-bold text-emerald-600 focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-emerald-400" placeholder="0.00">
                </div>
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Observaciones (Opcional)</label>
                    <input type="text" name="observaciones" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Faltantes o anomalías...">
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="modalCerrar = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-600">Confirmar Cierre</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('arqueoData', (montoEsperado) => ({
        esperado: montoEsperado,
        totalContado: 0,
        diferencia: -montoEsperado,
        monedas: {
            '0.01': { label: 'Centavos', val: 0.01, qty: '' },
            '0.05': { label: 'Reales', val: 0.05, qty: '' },
            '0.10': { label: 'Dieces', val: 0.10, qty: '' },
            '0.25': { label: 'Cuaras', val: 0.25, qty: '' },
            '0.50': { label: 'Pesos', val: 0.50, qty: '' },
            '1.00': { label: 'M. de $1', val: 1.00, qty: '' }
        },
        billetes: {
            '1': { val: 1, qty: '' },
            '5': { val: 5, qty: '' },
            '10': { val: 10, qty: '' },
            '20': { val: 20, qty: '' },
            '50': { val: 50, qty: '' },
            '100': { val: 100, qty: '' }
        },
        estadoClase: 'border-brand-200 bg-brand-50 text-brand-800 dark:border-brand-800 dark:bg-brand-900/20 dark:text-brand-300',
        estadoIconBg: 'bg-brand-100 text-brand-600 dark:bg-brand-800',
        estadoIconText: 'text-brand-600 dark:text-brand-300',
        estadoTextTitle: 'text-brand-800 dark:text-brand-300',
        estadoTextDesc: 'text-brand-600/80 dark:text-brand-300/80',
        estadoMensaje: 'Iniciar Conteo',
        estadoDesc: 'Ingresa las cantidades de cada denominación para el arqueo.',
        estadoIconPath: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',

        calcular() {
            let total = 0;
            for (let k in this.monedas) { let q = this.monedas[k].qty; if(q > 0) total += (q * this.monedas[k].val); }
            for (let k in this.billetes) { let q = this.billetes[k].qty; if(q > 0) total += (q * this.billetes[k].val); }
            
            this.totalContado = total;
            this.diferencia = parseFloat((total - this.esperado).toFixed(2));
            this.actualizarUI();
        },
        resetear() {
            for (let k in this.monedas) this.monedas[k].qty = '';
            for (let k in this.billetes) this.billetes[k].qty = '';
            this.calcular();
        },
        actualizarUI() {
            if (this.totalContado === 0) {
                this.estadoMensaje = 'Iniciar Conteo';
                this.estadoDesc = 'Ingresa las cantidades para comenzar a calcular.';
                this.estadoClase = 'border-brand-200 bg-brand-50'; this.estadoIconBg = 'bg-brand-100'; this.estadoIconText = 'text-brand-600'; this.estadoTextTitle = 'text-brand-800'; this.estadoTextDesc = 'text-brand-600';
                this.estadoIconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            } else if (this.diferencia === 0) {
                this.estadoMensaje = 'Cuadre Perfecto';
                this.estadoDesc = 'El efectivo coincide exactamente con lo esperado.';
                this.estadoClase = 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20'; this.estadoIconBg = 'bg-emerald-100 dark:bg-emerald-800'; this.estadoIconText = 'text-emerald-600 dark:text-emerald-300'; this.estadoTextTitle = 'text-emerald-800 dark:text-emerald-300'; this.estadoTextDesc = 'text-emerald-600/80 dark:text-emerald-300/80';
                this.estadoIconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            } else if (this.diferencia > 0) {
                this.estadoMensaje = 'Sobrante Detectado';
                this.estadoDesc = 'Hay $' + this.diferencia.toFixed(2) + ' de más en la caja.';
                this.estadoClase = 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20'; this.estadoIconBg = 'bg-amber-100 dark:bg-amber-800'; this.estadoIconText = 'text-amber-600 dark:text-amber-300'; this.estadoTextTitle = 'text-amber-800 dark:text-amber-300'; this.estadoTextDesc = 'text-amber-600/80 dark:text-amber-300/80';
                this.estadoIconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
            } else {
                this.estadoMensaje = 'Faltante Detectado';
                this.estadoDesc = 'Falta $' + Math.abs(this.diferencia).toFixed(2) + ' en la caja.';
                this.estadoClase = 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'; this.estadoIconBg = 'bg-red-100 dark:bg-red-800'; this.estadoIconText = 'text-red-600 dark:text-red-300'; this.estadoTextTitle = 'text-red-800 dark:text-red-300'; this.estadoTextDesc = 'text-red-600/80 dark:text-red-300/80';
                this.estadoIconPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
            }
        }
    }));
});
</script>
@endsection
