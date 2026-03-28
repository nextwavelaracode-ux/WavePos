@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-xl md:p-6">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detalle de Cierre de Caja</h2>
        <p class="text-sm text-gray-500">{{ $caja->fecha_apertura->format('d/m/Y H:i') }} → {{ $caja->fecha_cierre?->format('d/m/Y H:i') }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Resumen general --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Totales por método --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Desglose de Ingresos</h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach([
                        ['label'=>'Efectivo','val'=>$caja->total_efectivo,'color'=>'emerald'],
                        ['label'=>'Tarjeta','val'=>$caja->total_tarjeta,'color'=>'blue'],
                        ['label'=>'Transferencia','val'=>$caja->total_transferencia,'color'=>'purple'],
                        ['label'=>'Yappy','val'=>$caja->total_yappy,'color'=>'amber'],
                        ['label'=>'Crédito','val'=>$caja->total_credito,'color'=>'red'],
                    ] as $m)
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-3">
                        <p class="text-xs text-gray-400">{{ $m['label'] }}</p>
                        <p class="mt-1 text-lg font-bold text-gray-800 dark:text-white">${{ number_format($m['val'], 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Cuadre de caja --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Cuadre de Caja</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                        <dt class="text-gray-500">Monto Inicial</dt>
                        <dd class="font-semibold text-gray-800 dark:text-white">${{ number_format($caja->monto_inicial, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                        <dt class="text-gray-500">+ Efectivo Recibido</dt>
                        <dd class="font-semibold text-emerald-600">${{ number_format($caja->total_efectivo, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                        <dt class="text-gray-500">= Monto Esperado</dt>
                        <dd class="font-bold text-gray-800 dark:text-white">${{ number_format($caja->monto_inicial + $caja->total_efectivo, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                        <dt class="text-gray-500">Monto Real (contado)</dt>
                        <dd class="font-semibold text-gray-800 dark:text-white">${{ number_format($caja->monto_real_cierre, 2) }}</dd>
                    </div>
                    <div class="flex justify-between pt-1">
                        <dt class="font-bold text-gray-800 dark:text-white">Diferencia</dt>
                        <dd class="text-lg font-black
                            @if($caja->diferencia > 0) text-emerald-600
                            @elseif($caja->diferencia < 0) text-red-600
                            @else text-gray-400 @endif">
                            @if($caja->diferencia > 0)+@endif${{ number_format($caja->diferencia, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>

            @if($caja->observaciones)
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Observaciones</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $caja->observaciones }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Resumen</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Cajero</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $caja->usuario?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sucursal</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $caja->sucursal?->nombre ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Ventas</dt>
                        <dd class="font-bold text-brand-600 dark:text-brand-400 text-base">${{ number_format($caja->total_ventas, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">N° Transacciones</dt>
                        <dd class="font-semibold text-gray-800 dark:text-white">{{ $caja->ventas->where('estado','completada')->count() }}</dd>
                    </div>
                </dl>
            </div>
            <a href="{{ route('caja.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-300 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">
                ← Volver a Caja
            </a>
        </div>

    </div>
</div>
@endsection
