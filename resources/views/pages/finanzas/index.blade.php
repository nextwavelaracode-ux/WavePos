@extends('layouts.app')
@section('title', 'Dashboard Financiero')

@section('content')

<div class="px-4 py-8 mx-auto -mt-6">
    {{-- 1. Hero Banner --}}
    <x-finanzas.hero-banner />

    {{-- 2. KPI Cards --}}
    <x-finanzas.kpi-cards 
        :totalVentas="$totalVentas"
        :cuentasCobrar="$cuentasCobrar"
        :cuentasPagar="$cuentasPagar"
        :totalGastos="$totalGastos"
        :totalProductos="$totalProductos"
        :stockTotal="$stockTotal"
    />

    {{-- Filtro Global de Fechas y Sucursal --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-4 mb-6 relative z-20">
        <form method="GET" action="{{ route('finanzas.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-auto flex-1 md:flex-none">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                       class="w-full form-input rounded-xl text-sm border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white py-2">
            </div>
            <div class="w-full md:w-auto flex-1 md:flex-none">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                       class="w-full form-input rounded-xl text-sm border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white py-2">
            </div>
            
            <div class="w-full md:w-auto flex-1 md:flex-none hidden md:block">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sucursal</label>
                <select name="sucursal_id" class="w-full form-select rounded-xl text-sm border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white py-2 pr-8">
                    <option value="todas">Todas las sucursales</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ $sucursalId == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md transition-colors">
                    Actualizar Datos
                </button>
            </div>
        </form>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Columna Izquierda (Gráficas y Movimientos) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Gráfica de Ingresos vs Egresos --}}
            <x-finanzas.ingresos-chart :graficaPeriodos="$graficaPeriodos" />

            {{-- Movimientos de Inventario --}}
            <x-finanzas.movimientos-inventario :movimientos="$ultimosMovimientos" />

            {{-- Calendario Personal Recordatorios --}}
            <x-finanzas.calendario-recordatorios />
        </div>

        {{-- Columna Derecha (Cuentas y Vencimientos) --}}
        <div class="lg:col-span-4 space-y-6 flex flex-col">
            {{-- Panel Resumen Cuentas (Por Cobrar y Pagar) --}}
            <x-finanzas.cuentas-resumen :cuentasCobrar="$cuentasCobrarLista" :cuentasPagar="$cuentasPagarLista" />

            {{-- Resumen de Rentabilidad (Nuevo Widget) --}}
            <x-finanzas.resumen-rentabilidad 
                :utilidadBruta="$utilidadBruta"
                :utilidadNeta="$utilidadNeta"
                :margenGanancia="$margenGanancia"
                :totalVentas="$totalVentas"
                :totalCompras="$totalCompras"
                :totalGastos="$totalGastos"
            />

            {{-- Calendario de Vencimientos Automático --}}
            <div class="flex-1">
                <x-finanzas.calendario-vencimientos />
            </div>
        </div>

    </div>
</div>
@endsection
