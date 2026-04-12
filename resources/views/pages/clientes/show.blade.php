@extends('layouts.app')

@php
    $title = 'Ficha del Cliente | ' . $cliente->nombre_completo;
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    
    {{-- ── Header y Breadcrumbs ─────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Ficha del Cliente
            </h2>
            <nav class="mt-2">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('dashboard') }}">Dashboard /</a></li>
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('clientes.index') }}">Catálogo de Clientes /</a></li>
                    <li class="font-medium text-brand-600 dark:text-brand-400">Perfil</li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.history.back()"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white dark:focus:ring-offset-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Regresar
            </button>
            <a href="{{ route('clientes.index') }}?edit={{ $cliente->id }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-brand-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                Editar Perfil
            </a>
        </div>
    </div>

    {{-- ── Grid Principal de Ficha CRM ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 min-h-[70vh]">
        
        {{-- COLUMNA IZQUIERDA: Tarjeta Principal de Identidad ----------------- --}}
        <div class="lg:col-span-4 flex flex-col gap-6">
            
            {{-- Tarjeta de Perfil Central --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 p-8 flex flex-col items-center justify-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-brand-50 to-white dark:from-brand-900/20 dark:to-neutral-900 z-0"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    {{-- Avatar grande con marco --}}
                    <div class="h-32 w-32 rounded-full border-4 border-white dark:border-neutral-900 bg-brand-100 flex items-center justify-center text-4xl font-black text-brand-600 shadow-md dark:bg-brand-900/30 dark:text-brand-400 mb-5 relative">
                        {{ strtoupper(substr($cliente->nombre_completo, 0, 1)) }}
                        <span class="absolute bottom-1 right-3 h-5 w-5 rounded-full border-2 border-white dark:border-neutral-900 {{ $cliente->estado ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    </div>

                    <h2 class="text-2xl font-black text-gray-900 dark:text-white text-center tracking-tight">{{ $cliente->nombre_completo }}</h2>
                    <span class="inline-block mt-2 rounded-full bg-gray-100 px-3 py-1 text-[11px] font-bold text-gray-600 uppercase tracking-widest dark:bg-neutral-800 dark:text-gray-400">
                        {{ \App\Models\Cliente::tiposCliente()[$cliente->tipo_cliente] ?? 'Cliente' }}
                    </span>

                    <div class="mt-8 w-full space-y-4">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300 {{ !$cliente->telefono ? 'text-gray-400 italic' : '' }}">{{ $cliente->telefono ?: 'No registrado' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300 break-all w-full {{ !$cliente->email ? 'text-gray-400 italic' : '' }}">{{ $cliente->email ?: 'No registrado' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ficha de Estado Financiero Rápido --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 border-t-4 {{ $deudaActual > 0 ? 'border-t-amber-500' : 'border-t-emerald-500' }}">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4 dark:text-gray-400">Score de Pago</h3>
                <h4 class="text-3xl font-black tabular-nums tracking-tight {{ $deudaActual > 0 ? 'text-amber-600 dark:text-amber-500' : 'text-emerald-600 dark:text-emerald-500' }}">
                    ${{ number_format($deudaActual, 2) }}
                </h4>
                <p class="text-xs font-semibold text-gray-500 mt-1">Saldo Total en Mora / Pendiente</p>

                <div class="mt-5 space-y-3">
                    <div class="flex justify-between items-end border-b border-gray-50 dark:border-neutral-800/50 pb-2">
                        <span class="text-xs uppercase font-semibold tracking-widest text-gray-500">Cupo Límite Crédito</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums">${{ number_format($cliente->limite_credito, 2) }}</span>
                    </div>
                    @if($cliente->limite_credito > 0)
                        <div class="pt-2">
                            <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-neutral-800 overflow-hidden">
                                @php
                                    $porcDeuda = min(100, max(0, ($deudaActual / $cliente->limite_credito) * 100));
                                    $colorBar = $porcDeuda > 80 ? 'bg-red-500' : ($porcDeuda > 50 ? 'bg-amber-500' : 'bg-brand-500');
                                @endphp
                                <div class="{{ $colorBar }} h-full rounded-full transition-all duration-500" style="width: {{ $porcDeuda }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1.5">
                                <span class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Cupo Usado</span>
                                <span class="text-[10px] font-bold text-gray-500">{{ number_format($porcDeuda, 1) }}% Utilizado</span>
                            </div>
                        </div>
                    @else
                        <div class="pt-1">
                            <span class="inline-block rounded bg-gray-100 px-2 py-1 text-[10px] font-bold uppercase text-gray-500 dark:bg-neutral-800">Contado (Sin crédito asignado)</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Facturación Rápida / Aciones --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 border-l-4 border-l-brand-500">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500 mb-3 ml-1">Acciones Comerciales</p>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('facturacion.create') }}?cliente_id={{ $cliente->id }}" class="flex items-center justify-between rounded-xl px-4 py-3 bg-gray-50 border border-gray-100 hover:border-brand-300 hover:bg-brand-50 transition-colors group dark:bg-neutral-800/40 dark:border-neutral-700 dark:hover:border-brand-500/50">
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-brand-600 dark:text-gray-300 dark:group-hover:text-brand-400">Emitir Nueva Factura</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Pestañas Dinámicas (Info & Movimientos) ---------- --}}
        <div class="lg:col-span-8 flex flex-col gap-6" x-data="{ tab: 'informacion' }">
            
            {{-- Nav Tabs Estilo Moderno --}}
            <div class="rounded-2xl bg-white p-2 border border-gray-200 shadow-sm dark:bg-neutral-900 dark:border-neutral-800/80 flex overflow-x-auto hide-scrollbar">
                <button @click="tab = 'informacion'" :class="tab === 'informacion' ? 'bg-gray-100 text-gray-900 shadow-sm dark:bg-neutral-800 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Información General
                </button>
                <button @click="tab = 'historial'" :class="tab === 'historial' ? 'bg-gray-100 text-gray-900 shadow-sm dark:bg-neutral-800 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Facturas (<span x-text="'{{ $totalCompras }}'"></span>)
                </button>
                <button @click="tab = 'cuentas'" :class="tab === 'cuentas' ? 'bg-gray-100 text-gray-900 shadow-sm dark:bg-neutral-800 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2 relative">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Cuentas por Cobrar
                    @if($cliente->cuentasCobrar()->where('estado', '!=', 'pagado')->count() > 0)
                        <span class="absolute top-2.5 right-2 h-2 w-2 rounded-full bg-red-500"></span>
                    @endif
                </button>
            </div>

            {{-- 1. Tab de Información General --}}
            <div x-show="tab === 'informacion'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50/50 dark:bg-neutral-800/20 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-widest">Datos del Cliente</h3>
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $cliente->estado ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-neutral-800 dark:text-gray-400' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $cliente->estado ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/60">

                            {{-- BLOQUE: IDENTIFICACIÓN --}}
                            <tr class="bg-gray-50/60 dark:bg-neutral-800/10">
                                <td colspan="2" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Identificación</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400 w-2/5">Tipo de Cliente</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">
                                    {{ \App\Models\Cliente::tiposCliente()[$cliente->tipo_cliente] ?? $cliente->tipo_cliente }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Documento</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->documento_principal }}</td>
                            </tr>
                            @if(in_array($cliente->tipo_cliente, ['juridico', 'b2b']))
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Razón Social</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->empresa }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Representante Legal</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ trim($cliente->nombre . ' ' . $cliente->apellido) ?: '—' }}</td>
                            </tr>
                            @else
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Nombre Completo</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->nombre_completo }}</td>
                            </tr>
                            @endif
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Alta en Sistema</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->created_at->format('d/m/Y') }}</td>
                            </tr>

                            {{-- BLOQUE: CONTACTO --}}
                            <tr class="bg-gray-50/60 dark:bg-neutral-800/10">
                                <td colspan="2" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Contacto</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Teléfono</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->telefono ?: '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Email</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right break-all">{{ $cliente->email ?: '—' }}</td>
                            </tr>

                            {{-- BLOQUE: UBICACIÓN --}}
                            <tr class="bg-gray-50/60 dark:bg-neutral-800/10">
                                <td colspan="2" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Ubicación</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Dirección</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->direccion ?: '—' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Zona / Distrito</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">
                                    {{ $cliente->distrito ?: '—' }}{{ $cliente->provincia ? ' / ' . $cliente->provincia : '' }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">País</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-900 dark:text-white text-right">{{ $cliente->pais ?: '—' }}</td>
                            </tr>

                            {{-- BLOQUE: CRÉDITO --}}
                            <tr class="bg-gray-50/60 dark:bg-neutral-800/10">
                                <td colspan="2" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Crédito</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">Cupo Límite</td>
                                <td class="px-5 py-2.5 font-black tabular-nums text-gray-900 dark:text-white text-right">${{ number_format($cliente->limite_credito, 2) }}</td>
                            </tr>

                            @if($cliente->notas)
                            {{-- BLOQUE: NOTAS --}}
                            <tr class="bg-gray-50/60 dark:bg-neutral-800/10">
                                <td colspan="2" class="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Notas CRM</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed bg-amber-50/50 dark:bg-amber-900/10">{{ $cliente->notas }}</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- 2. Tab de Historial de Ventas (Estilo Timeline/Tarjetas CRM) --}}
            <div x-cloak x-show="tab === 'historial'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 dark:border-neutral-800/80 dark:bg-neutral-800/20 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-widest">Historial de Facturas</h3>
                        <span class="text-xs font-bold bg-brand-100 text-brand-700 px-2.5 py-1 rounded-md dark:bg-brand-500/10 dark:text-brand-400">{{ $totalCompras }} factura(s) · ${{ number_format($totalGastado, 2) }} total</span>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-neutral-800/60">
                        @php $acumulado = 0; @endphp
                        @forelse($cliente->ventas as $venta)
                            @php $acumulado += $venta->total; @endphp
                            {{-- Tarjeta de Venta/Factura Individual --}}
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center p-4 transition-colors hover:bg-gray-50/60 dark:hover:bg-neutral-800/20 gap-3">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-lg {{ $venta->estado === 'completada' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400' }} flex flex-col items-center justify-center shrink-0">
                                        <span class="text-[9px] font-bold uppercase leading-none">{{ $venta->created_at->format('M') }}</span>
                                        <span class="text-base font-black leading-tight">{{ $venta->created_at->format('d') }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $venta->numero }}</p>
                                            @if($venta->facturaElectronica)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black uppercase bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                                    DIAN: {{ $venta->facturaElectronica->numero }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $venta->detalles->count() }} ítem(s)
                                            · {{ $venta->pagos->pluck('metodo')->unique()->implode(', ') ?: '—' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 sm:gap-6 pl-15 sm:pl-0">
                                    <div class="text-left sm:text-right">
                                        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-0.5">Factura</p>
                                        <p class="font-black tabular-nums text-gray-900 dark:text-white text-sm">${{ number_format($venta->total, 2) }}</p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400 mb-0.5">Acumulado</p>
                                        <p class="font-semibold tabular-nums text-brand-600 dark:text-brand-400 text-sm">${{ number_format($acumulado, 2) }}</p>
                                    </div>
                                    <div>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            {{ $venta->estado === 'completada' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' }}">
                                            {{ $venta->estado }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('caja.ventas.show', $venta->id) }}" class="text-xs font-bold text-brand-600 hover:text-brand-500 dark:text-brand-400 transition-colors">Ver →</a>
                                        @if($venta->facturaElectronica)
                                            <a href="{{ route('facturacion.show', $venta->facturaElectronica->id) }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-500 dark:text-blue-400 transition-colors">DIAN →</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 px-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Sin Facturas Registradas</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este cliente aún no registra compras en el sistema.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. Tab de Cuentas por Cobrar --}}
            <div x-cloak x-show="tab === 'cuentas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 dark:border-neutral-800/80 dark:bg-neutral-800/20 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-widest">Registro de Deudas</h3>
                        <a href="{{ route('cuentas.index') }}?cliente={{ $cliente->id }}" class="text-xs font-bold text-brand-600 hover:text-brand-500 dark:text-brand-400 px-2 py-1">Administrar Todo &rarr;</a>
                    </div>
                    
                    <div class="overflow-x-auto p-0">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50/50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-800/80">
                                <tr>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Documento F.</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest hidden sm:table-cell">Emisión / Vence</th>
                                    <th class="px-5 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Saldo Pto.</th>
                                    <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                                    <th class="px-5 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/60 transition-colors">
                                @forelse($cliente->cuentasCobrar as $cuenta)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10">
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-gray-900 dark:text-white block">Cuenta #{{ $cuenta->id }}</span>
                                        <span class="text-xs font-mono text-gray-500 mt-0.5 block">Venta: {{ $cuenta->venta->numero }}</span>
                                    </td>
                                    <td class="px-5 py-4 hidden sm:table-cell">
                                        <span class="text-gray-700 dark:text-gray-300 block text-xs">{{ $cuenta->fecha_emision?->format('d/m/Y') ?? '—' }}</span>
                                        <span class="font-semibold text-xs mt-0.5 block {{ $cuenta->fecha_vencimiento && $cuenta->fecha_vencimiento->isPast() && $cuenta->saldo_pendiente > 0 ? 'text-red-500' : 'text-gray-500' }}">Vence: {{ $cuenta->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="font-black tabular-nums {{ $cuenta->saldo_pendiente > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-emerald-500' }}">${{ number_format($cuenta->saldo_pendiente, 2) }}</span>
                                        <span class="block text-[10px] text-gray-400 font-bold uppercase mt-0.5">De ${{ number_format($cuenta->total, 2) }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-black uppercase ring-1 ring-inset {{ $cuenta->status_color }}">
                                            {{ $cuenta->estado }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('cuentas.show', $cuenta->id) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white shadow-sm inline-block">Ver</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">No existen cuentas por cobrar generadas para este cliente.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<style>[x-cloak] { display: none !important; }</style>
@endsection
